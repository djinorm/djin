<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;
use ReflectionException;

class ObjectMapper implements MapperInterface
{

    /**
     * @var string
     */
    private $classname;
    /**
     * @var MapperInterface[]
     */
    private $mappers;

    /**
     * ObjectMapper constructor.
     * @param string $classname
     * @param MapperInterface[] $mappers
     */
    public function __construct(string $classname, array $mappers)
    {
        $this->classname = $classname;
        $this->mappers = $mappers;
    }

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return object
     * @throws HydratorException
     * @throws ReflectionException
     */
    public function hydrate($data)
    {
        if (!is_array($data)) {
            $type = gettype($data);
            throw new HydratorException("Class {$this->classname} can not be hydrated from '{$type}' type");
        }

        $object = RepoHelper::newWithoutConstructor($this->classname);
        foreach ($this->mappers as $property => $mapper) {
            $value = $data[$property] ?? null;
            RepoHelper::setProperty(
                $object,
                $property,
                $mapper->hydrate($value)
            );
        }

        return $object;
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return array
     * @throws ExtractorException
     * @throws ReflectionException
     */
    public function extract($complex)
    {
        if (!is_object($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("Class {$this->classname} can not be extracted from '{$type}' type");
        }
        $data = [];
        foreach ($this->mappers as $property => $mapper) {
            $data[$property] = $mapper->extract(
                RepoHelper::getProperty(
                    $complex,
                    $property
                )
            );
        }

        return $data;
    }

}