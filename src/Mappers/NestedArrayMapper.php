<?php
/**
 * Created for DjinORM.
 * Datetime: 10.11.2017 15:17
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\Handler\MappersHandler;

class NestedArrayMapper extends AbstractMapper implements ArrayMapperInterface, NestedMapperInterface
{

    /**
     * @var MappersHandler
     */
    protected $nestedMapper;
    /**
     * @var bool
     */
    protected $allowNullNested;

    public function __construct(
        string $property,
        string $classname,
        array $mappers,
        bool $allowNull = false
    )
    {
        $this->property = $property;
        $this->nestedMapper = new MappersHandler($classname, $mappers);
        $this->allowNull = $allowNull;
    }

    /**
     * @param array $data
     * @param object $object
     * @return array|null
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?array
    {
        $property = $this->getProperty();

        if (!isset($data[$property]) || $data[$property] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('array', $object);
        }

        $array = array_map(function ($data) use ($object) {
            if (null === $data) {
                if ($this->isNullAllowed()) {
                    return null;
                }
                return new HydratorException("Null instead of nested object is not allowed in " . $this->getDescription($object));
            }
            return $this->nestedMapper->hydrate($data);
        }, $data[$property]);

        RepoHelper::setProperty($object, $this->getProperty(), $array);
        return $array;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        $array = RepoHelper::getProperty($object, $this->getProperty());

        if (!is_array($array) && !is_a($array, \ArrayAccess::class)) {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('array', $object);
            }
            return [
                $this->getProperty() => null
            ];
        }

        $array = array_map(function ($nestedObject) use ($object) {
            if (null === $nestedObject) {
                if ($this->isNullAllowed()) {
                    return null;
                }
                new ExtractorException("Impossible to save null instead of nested object from " . $this->getDescription($object));
            }
            return $this->nestedMapper->extract($nestedObject);
        }, $array);

        return [
            $this->getProperty() => $array
        ];
    }

    /**
     * @return MappersHandler
     */
    public function getNestedMappersHandler(): MappersHandler
    {
        return $this->nestedMapper;
    }

}