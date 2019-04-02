<?php
/**
 * Created for DjinORM.
 * Datetime: 05.02.2018 15:11
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Handler;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\ArrayMapperInterface;
use DjinORM\Djin\Mappers\MapperInterface;
use DjinORM\Djin\Mappers\NestedMapperInterface;

class MappersHandler implements MappersHandlerInterface
{
    /**
     * @var string
     */
    protected $classname;
    /**
     * @var MapperInterface[]
     */
    protected $mappers;

    /** @var array */
    private $scheme;

    /**
     * Mapper constructor.
     * @param string $classname
     * @param MapperInterface[] $mappers
     */
    public function __construct(string $classname, array $mappers)
    {
        $this->classname = $classname;
        foreach ($mappers as $mapper) {
            $this->mappers[$mapper->getProperty()] = $mapper;
        }
    }

    /**
     * @return string class name of mapped object
     */
    public function getModelClassName(): string
    {
        return $this->classname;
    }

    /**
     * @return MapperInterface[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    /**
     * @param array $data
     * @param null $object
     * @return mixed
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object = null)
    {
        $model = $object ? $object : RepoHelper::newWithoutConstructor($this->classname);
        foreach ($this->mappers as $mapper) {
            $mapper->hydrate($data, $model);
        }
        return $model;
    }

    public function extract($model): array
    {
        $data = [];
        foreach ($this->mappers as $mapper) {
            $data = array_merge($data, $mapper->extract($model));
        }
        return $data;
    }

    /**
     * This method allow you to get mapper by model property name
     * @param string $property - model property. Can be nested, for example: profile.firstName
     * @return MapperInterface|null
     */
    public function getMapperByProperty(string $property): ?MapperInterface
    {
        return $this->getMapperRecursive($property, $this);
    }

    public function getScheme(): array
    {
        if (!$this->scheme) {
            $this->scheme = $this->getSchemeRecursive('', $this);
        }
        return $this->scheme;
    }

    protected function getSchemeRecursive(string $prefix, MappersHandlerInterface $mappersHandler)
    {
        $map = [];
        foreach ($mappersHandler->getMappers() as $mapper) {

            $value = $mapper instanceof ArrayMapperInterface ? [] : null;
            $path = "{$prefix}{$mapper->getProperty()}";
            $map[$path] = $value;

            if ($mapper instanceof NestedMapperInterface) {
                $subProperties = $this->getSchemeRecursive($path . '.', $mapper->getNestedMappersHandler());
                foreach ($subProperties as $subProperty => $subValue) {
                    $map[$subProperty] = $subValue;
                }
                continue;
            }

        }
        return $map;
    }

    /**
     * @param string $property
     * @param MappersHandlerInterface $mappersHandler
     * @return MapperInterface
     */
    protected function getMapperRecursive(string $property, MappersHandlerInterface $mappersHandler): ?MapperInterface
    {
        $path = explode('.', $property);
        $property = $path[0];
        unset($path[0]);

        $mapper = $mappersHandler->getMappers()[$property] ?? null;

        if (empty($path)) {
            return $mapper;
        }

        if ($mapper instanceof NestedMapperInterface) {
            return $this->getMapperRecursive(
                implode('.', $path),
                $mapper->getNestedMappersHandler()
            );
        }

        return null;
    }
}