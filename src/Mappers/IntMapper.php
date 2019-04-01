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

class IntMapper extends ScalarMapper
{

    /**
     * @param array $data
     * @param object $object
     * @return int
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?int
    {
        $property = $this->getProperty();
        if (!isset($data[$property])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('integer', $object);
        }

        $value = (int) $data[$property];
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
            throw $this->nullExtractorException('integer', $object);
        }

        $value = is_null($value) ? null : (int) $value;

        return [
            $this->getProperty() => $value
        ];
    }

}