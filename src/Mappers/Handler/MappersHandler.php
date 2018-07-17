<?php
/**
 * Created for DjinORM.
 * Datetime: 05.02.2018 15:11
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Handler;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\ArrayMapper;
use DjinORM\Djin\Mappers\MapperInterface;
use DjinORM\Djin\Mappers\SubclassMapper;

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
     * @param array $mappers
     */
    public function __construct(string $classname, array $mappers)
    {
        $this->classname = $classname;
        $this->mappers = $mappers;
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
     * @return mixed
     * @throws \ReflectionException
     */
    public function hydrate(array $data)
    {
        $model = RepoHelper::newWithoutConstructor($this->classname);
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
    public function modelPropertiesToDbAliases(): array
    {
        if (null === $this->map) {
            $this->map = $this->recursiveModelPropertiesToDbAliases($this);
        }
        return $this->map;
    }

    /**
     * @see modelPropertiesToDbAliases()
     * @param string $property
     * @return string
     */
    public function modelPropertyToDbAlias(string $property): string
    {
        return $this->modelPropertiesToDbAliases()[$property];
    }

    protected function recursiveModelPropertiesToDbAliases(MappersHandlerInterface $mappersHandler)
    {
        $map = [];
        foreach ($mappersHandler->getMappers() as $mapper) {

            if ($mapper instanceof ArrayMapper && $mapper->getNestedMappersHandler()) {
                $property = $mapper->getModelProperty();
                $dbAlias = $mapper->getDbAlias();
                $subMap = $this->recursiveModelPropertiesToDbAliases($mapper->getNestedMappersHandler());
                foreach ($subMap as $subProperty => $subDbAlias) {
                    $map["{$property}.{$subProperty}"] = "{$dbAlias}.{$subDbAlias}";
                }
                continue;
            }

            if ($mapper instanceof SubclassMapper) {
                $property = $mapper->getModelProperty();

                $dbAlias = $mapper->getDbAlias();
                if (mb_strlen($dbAlias) > 0) {
                    $dbAlias.= '.';
                }

                $subMap = $this->recursiveModelPropertiesToDbAliases($mapper->getNestedMappersHandler());
                foreach ($subMap as $subProperty => $subDbAlias) {
                    $map["{$property}.{$subProperty}"] = $dbAlias.$subDbAlias;
                }
                continue;
            }

            $map[$mapper->getModelProperty()] = $mapper->getDbAlias();
        }
        return $map;
    }

}