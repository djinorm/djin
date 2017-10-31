<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

abstract class ScalarMapper implements MapperInterface
{

    /**
     * @var string
     */
    protected $modelProperty;

    /**
     * @var string
     */
    protected $dbColumn;

    /**
     * @var bool
     */
    protected $allowNull;

    /**
     * Mapper constructor.
     * @param string $modelProperty
     * @param string $dbColumn
     * @param bool $allowNull
     */
    public function __construct(string $modelProperty, string $dbColumn = null, bool $allowNull = false)
    {
        $this->modelProperty = $modelProperty;
        $this->dbColumn = $dbColumn ?? $modelProperty;
        $this->allowNull = $allowNull;
    }

    /**
     * @return string
     */
    public function getModelProperty(): string
    {
        return $this->modelProperty;
    }

    /**
     * @return string
     */
    public function getDbColumn(): string
    {
        return $this->dbColumn;
    }

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allowNull;
    }

    protected function getDescription($object): string
    {
        return get_class($object) . '::' . $this->getModelProperty();
    }

    /**
     * @param string $expectedType
     * @param $object
     * @return HydratorException
     */
    protected function nullHydratorException(string $expectedType, $object): HydratorException
    {
        return new HydratorException("Null instead of {$expectedType} is not allowed in " . $this->getDescription($object));
    }

    /**
     * @param string $expectedType
     * @param $object
     * @return ExtractorException
     */
    protected function nullExtractorException(string $expectedType, $object): ExtractorException
    {
        return new ExtractorException("Impossible to save null instead of {$expectedType} from " . $this->getDescription($object));
    }


}