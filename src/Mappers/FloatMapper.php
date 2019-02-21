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

class FloatMapper extends ScalarMapper
{

    /**
     * @param array $data
     * @param object $object
     * @return float
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?float
    {
        $column = $this->getDbAlias();

        if (!isset($data[$column])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('float/double', $object);
        }

        $value = (float) $data[$column];
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
            throw $this->nullExtractorException('float/double', $object);
        }

        $value = is_null($value) ? null : (float) $value;

        return [
            $this->getDbAlias() => $value
        ];
    }
}