<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 13:42
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

abstract class AbstractMapper implements MapperInterface
{

    /**
     * @var string
     */
    protected $modelProperty;

    /**
     * @var string
     */
    protected $dbAlias;

    /**
     * @var bool
     */
    protected $allowNull;

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
    public function getDbAlias(): string
    {
        return $this->dbAlias;
    }

    /**
     * @return bool
     */
    public function isNullAllowed(): bool
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