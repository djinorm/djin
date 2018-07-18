<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 18:38
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;


interface NotationInterface
{

    public function isDecodeFirst(): bool;

    /**
     * @param $array
     * @return mixed
     */
    public function encode(array $array);

    /**
     * @param $encodedValue
     * @return array
     */
    public function decode($encodedValue): array;

}