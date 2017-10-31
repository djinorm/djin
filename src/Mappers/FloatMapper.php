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
     */
    public function hydrate(array $data, $object): ?float
    {
        $column = $this->getDbColumn();

        if (!isset($data[$column])) {
            if ($this->isAllowNull()) {
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
     * @param $object
     * @return array
     * @throws ExtractorException
     */
    public function extract($object): array
    {
        /** @var int $value */
        $value = RepoHelper::getProperty($object, $this->getModelProperty());

        if ($value === null && $this->isAllowNull() == false) {
            throw $this->nullExtractorException('float/double', $object);
        }

        return [
            $this->getDbColumn() => (float) $value
        ];
    }

    public function getFixtures(): array
    {
        $fixtures = range(0, 5, 0.5);

        if ($this->isAllowNull()) {
            $fixtures[] = null;
        }

        return $fixtures;
    }
}