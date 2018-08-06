<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;

class StringMapper extends ScalarMapper
{
    /**
     * @var int|null
     */
    private $maxLength;

    public function __construct($modelProperty, $maxLength = null, $allowNull = false, $dbAlias = null)
    {
        parent::__construct($modelProperty, $allowNull, $dbAlias);
        $this->maxLength = $maxLength;
    }

    /**
     * @param array $data
     * @param object $object
     * @return string
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?string
    {
        $column = $this->getDbAlias();
        if (!isset($data[$column])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('string', $object);
        }

        $value = (string) $data[$column];
        RepoHelper::setProperty($object, $this->getModelProperty(), $value);
        return $value;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        /** @var int $value */
        $value = RepoHelper::getProperty($object, $this->getModelProperty());

        if ($value === null && $this->isNullAllowed() == false) {
            throw $this->nullExtractorException('string', $object);
        }

        if ($this->maxLength && mb_strlen($value) > $this->maxLength) {
            throw new ExtractorException('String value of ' . $this->getDescription($object) . ' is too much. Max is: ' . $this->maxLength);
        }

        return [
            $this->getDbAlias() => (string) $value
        ];
    }

}