<?php
/**
 * Created for DjinORM
 * Datetime: 26.06.2019 19:00
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Helpers\RepoHelper;
use ReflectionClass;
use ReflectionException;

class ValueObjectMapper implements MapperInterface
{

    /**
     * @var string
     */
    private $classname;
    /**
     * @var MapperInterface
     */
    private $mapper;
    /**
     * @var string
     */
    private $property;

    /**
     * ValueObjectMapper constructor.
     * @param string $classname
     * @param MapperInterface $mapper
     * @param string|null $property
     * @throws LogicException
     * @throws ReflectionException
     */
    public function __construct(string $classname, MapperInterface $mapper, string $property = null)
    {
        $this->classname = $classname;
        $this->mapper = $mapper;
        if ($property === null) {
            $reflectionClass = new ReflectionClass($classname);
            $reflectionProperties = $reflectionClass->getProperties();
            if (count($reflectionProperties) !== 1) {
                throw new LogicException("ValueObject '{$classname}' contain more non one property. PLease, define property manually");
            }
            $this->property = current($reflectionProperties)->getName();
        }
    }


    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return object
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        try {
            $value = $this->mapper->hydrate($data);
        } catch (HydratorException $exception) {
            throw new HydratorException(
                "ValueObject '{$this->classname}': {$exception->getMessage()}",
                $exception->getCode()
            );
        }

        return new ($this->classname)($value);
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return object
     * @throws ExtractorException
     * @throws ReflectionException
     */
    public function extract($complex)
    {
        if (!is_object($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("Can not extract '{$this->classname}' ValueObject value from type '{$type}'");
        }

        $value = RepoHelper::getProperty($complex, $this->property);
        return $this->mapper->extract($value);
    }
}