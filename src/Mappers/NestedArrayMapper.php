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
use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

class NestedArrayMapper extends AbstractMapper implements ArrayMapperInterface, NestedMapperInterface
{

    /**
     * @var MappersHandlerInterface
     */
    protected $nestedMapper;
    /**
     * @var bool
     */
    protected $allowNullNested;

    public function __construct(
        string $modelProperty,
        string $dbAlias = null,
        string $classname,
        array $mappers,
        bool $allowNull = false
    )
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias ?? $modelProperty;
        $this->allowNull = $allowNull;
        $this->nestedMapper = new MappersHandler($classname, $mappers);
    }

    /**
     * @param array $data
     * @param object $object
     * @return array|null
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object): ?array
    {
        $column = $this->getDbAlias();

        if (!isset($data[$column]) || $data[$column] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
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
        }, $data[$column]);

        RepoHelper::setProperty($object, $this->getModelProperty(), $array);
        return $array;
    }

    /**
     * @param $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        $array = RepoHelper::getProperty($object, $this->getModelProperty());

        if (!is_array($array) && !is_a($array, \JsonSerializable::class)) {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('array', $object);
            }
            return [
                $this->getDbAlias() => null
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
            $this->getDbAlias() => $array
        ];
    }

    /**
     * @return MappersHandlerInterface
     */
    public function getNestedMappersHandler(): MappersHandlerInterface
    {
        return $this->nestedMapper;
    }

}