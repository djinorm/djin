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

class ArrayMapper extends AbstractMapper implements ArrayMapperInterface
{

    /**
     * @var bool
     */
    protected $allowNullNested;

    public function __construct(
        string $modelProperty,
        bool $allowNull = false,
        string $dbAlias = null
    )
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias ?? $modelProperty;
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
        $column = $this->getDbAlias();

        if (!isset($data[$column]) || $data[$column] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('array', $object);
        }

        $array = $data[$column];

        RepoHelper::setProperty($object, $this->getModelProperty(), $array);
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
        $array = RepoHelper::getProperty($object, $this->getModelProperty());

        if (!is_array($array)) {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('array', $object);
            }
            return [
                $this->getDbAlias() => null
            ];
        }

        return [
            $this->getDbAlias() => $array
        ];
    }
}