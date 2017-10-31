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
use DjinORM\Djin\Id\Id;

class IdMapper extends ScalarMapper
{

    /**
     * @param array $data
     * @param object $object
     * @return Id|null
     * @throws HydratorException
     */
    public function hydrate(array $data, $object): ?Id
    {
        $column = $this->getDbColumn();
        if (!isset($data[$column])) {
            if ($this->isAllowNull()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('Id', $object);
        }

        $id = new Id($data[$column]);
        RepoHelper::setProperty($object, $this->getModelProperty(), $id);
        return $id;
    }

    /**
     * @param $object
     * @return array
     * @throws ExtractorException
     */
    public function extract($object): array
    {
        /** @var Id $id */
        $id = RepoHelper::getProperty($object, $this->getModelProperty());

        if ($id === null) {
            if ($this->isAllowNull() == false) {
                throw $this->nullExtractorException('Id', $object);
            }
            return [
                $this->getDbColumn() => null
            ];
        }

        return [
            $this->getDbColumn() => $id->toScalar()
        ];
    }

    public function getFixtures(): array
    {
        $fixtures = range(0, 9);

        if ($this->isAllowNull()) {
            $fixtures[] = null;
        }

        return $fixtures;
    }
}