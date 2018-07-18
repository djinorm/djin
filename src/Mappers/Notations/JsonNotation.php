<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 18:38
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;


class JsonNotation implements NotationInterface
{

    /**
     * @param array $array
     * @return mixed
     */
    public function encode(array $array)
    {
        return json_encode($array);
    }

    /**
     * @param $encodedValue
     * @return array
     */
    public function decode($encodedValue): array
    {
        return json_decode($encodedValue, true);
    }
}