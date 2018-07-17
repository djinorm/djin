<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


interface MapperInterface
{

    /**
     * @param array $data
     * @param object $object
     * @return mixed
     */
    public function hydrate(array $data, $object);

    /**
     * @param $object
     * @return array
     */
    public function extract($object): array;

    /**
     * @return string
     */
    public function getModelProperty(): string;

    /**
     * @return string
     */
    public function getDbAlias(): string;

}