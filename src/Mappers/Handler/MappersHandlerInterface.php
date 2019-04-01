<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers\Handler;


use DjinORM\Djin\Mappers\MapperInterface;
use DjinORM\Djin\Model\ModelInterface;

interface MappersHandlerInterface
{

    /**
     * @return string class name of mapped object
     */
    public function getModelClassName(): string;

    /**
     * @return MapperInterface[]
     */
    public function getMappers(): array;

    /**
     * @param array $data
     * @param object|null $object
     * @return mixed
     */
    public function hydrate(array $data, $object = null);


    /**
     * @param ModelInterface $model
     * @return array
     */
    public function extract($model): array;

    /**
     * This method allow you to get mapper by model property name
     * @param string $property - model property. Can be nested, for example: profile.firstName
     * @return MapperInterface|null
     */
    public function getMapperByProperty(string $property): ?MapperInterface;

}