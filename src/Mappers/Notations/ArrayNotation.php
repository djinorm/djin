<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 18:39
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;


class ArrayNotation implements NotationInterface
{

    /**
     * @param array $array
     * @return mixed
     */
    public function encode(array $array)
    {
        return $array;
    }

    /**
     * @param $encodedValue
     * @return array
     */
    public function decode($encodedValue): array
    {
        return $encodedValue;
    }
}