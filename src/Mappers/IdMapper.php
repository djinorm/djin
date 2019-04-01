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
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?Id
    {
        $property = $this->getProperty();
        if (!isset($data[$property])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('Id', $object);
        }

        $id = new Id($data[$property]);
        RepoHelper::setProperty($object, $this->getProperty(), $id);
        return $id;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        /** @var Id $id */
        $id = RepoHelper::getProperty($object, $this->getProperty());

        if ($id === null) {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('Id', $object);
            }
            return [
                $this->getProperty() => null
            ];
        }

        return [
            $this->getProperty() => $id->toScalar()
        ];
    }
}