<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 11:28
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;

class BoolMapper extends ScalarMapper
{

    /**
     * @param array $data
     * @param object $object
     * @return bool
     * @throws \DjinORM\Djin\Exceptions\HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?bool
    {
        $property = $this->getProperty();

        if (!isset($data[$property])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('bool', $object);
        }

        $value = (bool) $data[$property];

        RepoHelper::setProperty($object, $this->getProperty(), $value);
        return $value;
    }

    /**
     * @param object $object
     * @return array
     * @throws \DjinORM\Djin\Exceptions\ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        /** @var bool $value */
        $value = RepoHelper::getProperty($object, $this->getProperty());

        if ($value === null && $this->isNullAllowed() == false) {
            throw $this->nullExtractorException('bool', $object);
        }

        $value = is_null($value) ? null : (bool) $value;

        return [
            $this->getProperty() => $value
        ];
    }

}
