<?php
/**
 * Created for DjinORM.
 * Datetime: 12.12.2018 17:00
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Id\Id;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class DeepIdentityMapper
 * @package DjinORM\Djin\Mappers
 *
 * This mapper can hydrate and extract any types of data by Reflection. All that you need - pass unique string
 * identities for class names, than can be hydrated and extracted. This mapper support multi level nested arrays
 * and objects, that can be fully serialized
 */
class DeepIdentityMapper extends AbstractMapper implements ArrayMapperInterface
{

    protected const IDENTITY_KEY = '___{identity}___';

    /**
     * @var array
     */
    private $classMap;
    /**
     * @var array
     */
    private $identityMap;
    /**
     * @var int
     */
    private $reflectionProperties;
    /**
     * @var ReflectionClass[]
     */
    private $reflectionClasses = [];

    /**
     * DeepIdentityMapper constructor.
     * @param string $property
     * @param array $map
     * @param array $reflectionProperties
     * @param bool $allowNull
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $property,
        array $map,
        array $reflectionProperties = [
            ReflectionProperty::IS_PUBLIC,
            ReflectionProperty::IS_PROTECTED,
            ReflectionProperty::IS_PRIVATE,
        ],
        bool $allowNull = false
    )
    {
        $this->property = $property;
        $this->allowNull = $allowNull;

        foreach ($map as $className => $callableOrIdentity) {
            $value = is_callable($callableOrIdentity) ? $callableOrIdentity($className) : $callableOrIdentity;
            $this->classMap[$className] = $value;
        }

        if (!isset($this->classMap[Id::class])) {
            $this->classMap[Id::class] = 'id';
        }

        $this->identityMap = array_flip($this->classMap);

        if (count($this->classMap) != count($this->identityMap)) {
            throw new InvalidArgumentException('Mismatch count of $classMap and $identityMap');
        }

        $this->reflectionProperties =  array_reduce($reflectionProperties, function($a, $b) { return $a | $b; }, 0);
    }

    /**
     * @param array $data
     * @param object $object
     * @return mixed
     * @throws HydratorException
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws ReflectionException
     */
    public function hydrate(array $data, object $object)
    {
        $property = $this->getProperty();

        if (!isset($data[$property])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException("{$property} value", $object);
        }

        $value = $this->hydrateRecursive($data[$property]);
        RepoHelper::setProperty($object, $this->getProperty(), $value);
        return $value;
    }

    /**
     * @param $data
     * @return array|object
     * @throws HydratorException
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws ReflectionException
     */
    protected function hydrateRecursive($data)
    {
        if (is_array($data)) {
            if (!isset($data[static::IDENTITY_KEY])) {
                $array = [];
                foreach ($data as $key => $value) {
                    $array[$key] = $this->hydrateRecursive($value);
                }
                return $array;
            }

            if (isset($this->identityMap[$data[static::IDENTITY_KEY]])) {
                $class = $this->identityMap[$data[static::IDENTITY_KEY]];

                if ($class == Id::class) {
                    return new Id($data['data']['id']);
                }

                $reflection = $this->getReflectionClass($class, HydratorException::class);
                $object = $reflection->newInstanceWithoutConstructor();

                $reflectionProperties = $reflection->getProperties($this->reflectionProperties);
                foreach ($reflectionProperties as $reflectionProperty) {
                    if (isset($data['data'][$reflectionProperty->getName()])) {
                        $reflectionProperty->setAccessible(true);
                        $value = $data['data'][$reflectionProperty->getName()];
                        $reflectionProperty->setValue($object, $this->hydrateRecursive($value));
                    }
                }
                return $object;

            } else {
                throw new HydratorException("Trying to hydrate corrupted data. Invalid identity key '{$data[static::IDENTITY_KEY]}'");
            }
        }

        return $data;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws ReflectionException
     */
    public function extract(object $object): array
    {
        /** @var int $value */
        $value = RepoHelper::getProperty($object, $this->getProperty());

        if ($value === null) {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException("{$this->getProperty()} type", $object);
            }
            return [
                $this->getProperty() => null
            ];
        }

        return [
            $this->getProperty() => $this->extractRecursive($value),
        ];
    }

    /**
     * @param $something
     * @return array|mixed
     * @throws ExtractorException
     * @throws ReflectionException
     */
    protected function extractRecursive($something)
    {
        if (is_array($something)) {
            $data = [];
            foreach ($something as $key => $value) {
                $data[$key] = $this->extractRecursive($value);
            }

            return $data;

        } elseif (is_object($something)) {
            $class = get_class($something);
            $reflection = $this->getReflectionClass($class, ExtractorException::class);

            $data = [
                static::IDENTITY_KEY => $this->classMap[$class],
                'data' => [],
            ];

            if ($something instanceof Id) {
                $data['data']['id'] = $something->getPermanentOrNull();
                return $data;
            }

            $reflectionProperties = $reflection->getProperties($this->reflectionProperties);
            foreach ($reflectionProperties as $reflectionProperty) {
                $reflectionProperty->setAccessible(true);
                $value = $reflectionProperty->getValue($something);
                $data['data'][$reflectionProperty->getName()] = $this->extractRecursive($value);
            }

            return $data;

        } elseif (is_scalar($something) || is_null($something)) {
            return $something;
        }

        $type = gettype($something);
        throw new ExtractorException("Non-extractable type {$type}");
    }

    /**
     * @param $class
     * @param string $exceptionClass
     * @return ReflectionClass
     * @throws ReflectionException
     */
    protected function getReflectionClass($class, string $exceptionClass): ReflectionClass
    {
        if (!isset($this->classMap[$class])) {
            throw new $exceptionClass("Unknown class {$class} in " . __CLASS__);
        }

        if (!isset($this->reflectionClasses[$class])) {
            $this->reflectionClasses[$class] = new ReflectionClass($class);
        }
        return $this->reflectionClasses[$class];
    }

}