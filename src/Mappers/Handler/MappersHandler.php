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
    private $map;

    /**
     * Mapper constructor.
     * @param string $classname
     * @param MapperInterface[] $mappers
     */
    public function __construct(string $classname, array $mappers)
    {
        $this->classname = $classname;
        foreach ($mappers as $mapper) {
            $this->mappers[$mapper->getModelProperty()] = $mapper;
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
     * @return string[] representation mappers as dot-notation. For example, we have UserModel with id and email, and
     * nested Profile witch contain firstName and lastName values. It can be represented like
     * [
     *      'id' => 'user_id',
     *      'email' => 'email',
     *      'profile.firstName' => 'profile.first_name',
     *      'profile.lastName' => 'profile.last_name',
     * ]
     */
    public function getModelPropertiesToDbAliases(): array
    {
        if (null === $this->map) {
            $this->map = $this->recursiveModelPropertiesToDbAliases($this);
        }
        return $this->map;
    }

    /**
     * @see getModelPropertiesToDbAliases()
     * @param string $property
     * @return string
     */
    public function getModelPropertyToDbAlias(string $property): ?string
    {
        return $this->getModelPropertiesToDbAliases()[$property] ?? null;
    }

    protected function recursiveModelPropertiesToDbAliases(MappersHandlerInterface $mappersHandler)
    {
        $map = [];
        foreach ($mappersHandler->getMappers() as $mapper) {

            $isArrayMapper = $mapper instanceof ArrayMapperInterface && $mapper->getNestedMappersHandler();
            $isNestedMapper = $mapper instanceof NestedMapperInterface;
            if ($isArrayMapper || $isNestedMapper) {
                $property = $mapper->getModelProperty();
                $dbAlias = $mapper->getDbAlias();
                $subMap = $this->recursiveModelPropertiesToDbAliases($mapper->getNestedMappersHandler());
                $map["{$property}"] = "{$dbAlias}";
                foreach ($subMap as $subProperty => $subDbAlias) {
                    $map["{$property}.{$subProperty}"] = "{$dbAlias}.{$subDbAlias}";
                }
                continue;
            }

            $map[$mapper->getModelProperty()] = $mapper->getDbAlias();
        }
        return $map;
    }

    /**
     * @return string[] representation mappers as dot-notation. For example, we have UserModel with id and email, and
     * nested Profile witch contain firstName and lastName values. It can be represented like
     * [
     *      'id' => 'user_id',
     *      'email' => 'email',
     *      'profile_first_name' => 'profile.firstName',
     *      'profile_last_name' => 'profile.lastName',
     * ]
     */
    public function getDbAliasesToModelProperties(): array
    {
        return array_flip($this->getModelPropertiesToDbAliases());
    }

    /**
     * @see getModelPropertiesToDbAliases()
     * @param string $property
     * @return string
     */
    public function getDbAliasToModelProperty(string $property): ?string
    {
        return $this->getDbAliasesToModelProperties()[$property] ?? null;
    }

    /**
     * This method allow you to get mapper by model property name
     * @param string $property - model property. Can be nested, for example: profile.firstName
     * @return MapperInterface|null
     */
    public function getMapperByModelProperty(string $property): ?MapperInterface
    {
        return $this->getMapperRecursive($property, $this);
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

        if ($mapper instanceof ArrayMapperInterface) {
            if ($mapper->getNestedMappersHandler() === null) {
                return null;
            }
            return $this->getMapperRecursive(
                implode('.', $path),
                $mapper->getNestedMappersHandler()
            );
        }

        if ($mapper instanceof NestedMapperInterface) {
            return $this->getMapperRecursive(
                implode('.', $path),
                $mapper->getNestedMappersHandler()
            );
        }

        return $mapper;
    }

    /**
     * This method allow you to get mapper by db alias name
     * @param string $dbAlias - can be nested, for example: profile.first_name
     * @return MapperInterface|null
     */
    public function getMapperByDbAlias(string $dbAlias): ?MapperInterface
    {
        $modelProperty = $this->getDbAliasToModelProperty($dbAlias);
        if ($modelProperty === null) {
            return null;
        }
        return $this->getMapperByModelProperty($modelProperty);
    }
}