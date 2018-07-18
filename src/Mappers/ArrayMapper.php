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
use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

class ArrayMapper extends AbstractMapper
{

    /**
     * @var MappersHandlerInterface
     */
    protected $nestedMapper;
    /**
     * @var bool
     */
    protected $allowNullNested;
    /**
     * @var bool
     */
    protected $asJsonString;

    public function __construct(
        string $modelProperty,
        string $dbAlias,
        bool $allowNull = false,
        bool $asJsonString = false,
        MappersHandlerInterface $nestedMapper = null,
        bool $allowNullNested = true
    )
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias;
        $this->allowNull = $allowNull;
        $this->nestedMapper = $nestedMapper;
        $this->allowNullNested = $allowNullNested;
        $this->asJsonString = $asJsonString;
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
            if ($this->isAllowNull()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('array', $object);
        }

        if (is_array($data[$column])) {
            $array = $data[$column];
        } elseif (is_string($data[$column])) {
            $array = \json_decode($data[$column], true);
            if ($array === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new HydratorException('Json parse error: ' . json_last_error_msg(), 1);
            }
        }

        if ($this->nestedMapper) {
            $array = array_map(function ($data) use ($object){
                if (null === $data) {
                    if ($this->isAllowNullNested()) {
                        return null;
                    }
                    return new HydratorException("Null instead of nested object is not allowed in " . $this->getDescription($object));
                }
                return $this->nestedMapper->hydrate($data);
            }, $array);
        }

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
            if ($this->isAllowNull() == false) {
                throw $this->nullExtractorException('array', $object);
            }
            return [
                $this->getDbAlias() => null
            ];
        }

        if ($this->nestedMapper) {
            $array = array_map(function ($nestedObject) use ($object) {
                if (null === $nestedObject) {
                    if ($this->isAllowNullNested()) {
                        return null;
                    }
                    new ExtractorException("Impossible to save null instead of nested object from " . $this->getDescription($object));
                }
                return $this->nestedMapper->extract($nestedObject);
            }, $array);
        }

        if ($this->asJsonString) {
            $array = \json_encode($array);
            if ($array === false && json_last_error() !== JSON_ERROR_NONE) {
                throw new ExtractorException('Json encode error: ' . json_last_error_msg(), 1);
            }
        }

        return [
            $this->getDbAlias() => $array
        ];
    }

    /**
     * @return MappersHandlerInterface
     */
    public function getNestedMappersHandler(): ?MappersHandlerInterface
    {
        return $this->nestedMapper;
    }

    /**
     * @return bool
     */
    public function isAllowNullNested(): bool
    {
        return $this->allowNullNested;
    }
}