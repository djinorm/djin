<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 30.10.2017 13:36
 */

namespace DjinORM\Djin\Repository;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\MapperInterface;
use DjinORM\Djin\Model\ModelInterface;

abstract class MapperRepository extends Repository
{

     /**
     * @return MapperInterface[]
     */
    abstract protected function map(): array;

    /**
     * Превращает массив в объект нужного класса
     * @param array $data
     * @return ModelInterface
     */
    protected function hydrate(array $data): ModelInterface
    {
        /** @var ModelInterface $model */
        $model = RepoHelper::newWithoutConstructor(static::getModelClass());
        foreach ($this->map() as $mapper) {
            $mapper->hydrate($data, $model);
        }
        return $model;
    }

    /**
     * @param ModelInterface $object
     * @return array
     */
    protected function extract(ModelInterface $object): array
    {
        $data = [];
        foreach ($this->map() as $mapper) {
            $data = array_merge($data, $mapper->extract($object));
        }
        return $data;
    }

}