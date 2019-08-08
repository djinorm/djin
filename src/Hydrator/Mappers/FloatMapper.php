<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Hydrator\Mappers;


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
        $property = $this->getProperty();

        if (!isset($data[$property])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('float/double', $object);
        }

        $value = (float) $data[$property];
        RepoHelper::setProperty($object, $this->getProperty(), $value);
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
        $value = RepoHelper::getProperty($object, $this->getProperty());

        if ($value === null && $this->isNullAllowed() == false) {
            throw $this->nullExtractorException('float/double', $object);
        }

        $value = is_null($value) ? null : (float) $value;

        return [
            $this->getProperty() => $value
        ];
    }
}