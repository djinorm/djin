<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Hydrator\Mappers;


interface MapperInterface
{

    /**
     * Превращает простой массив в объект нужного типа
     * @param array $data
     * @param object $object
     * @return mixed
     */
    public function hydrate(array $data, object $object);

    /**
     * Превращает объект в простой массив
     * @param $object
     * @return array
     */
    public function extract(object $object): array;

    /**
     * Имя свойства модели
     * @return string
     */
    public function getProperty(): string;

    /**
     * Разрешена ли передача null в качестве значения
     * @return bool
     */
    public function isNullAllowed(): bool;

}