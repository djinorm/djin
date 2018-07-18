<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 18:41
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;


use Adbar\Dot;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class DotNotation implements NotationInterface
{

    /**
     * @param $array
     * @return mixed
     */
    public function encode(array $array)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($array)
        );

        $encoded = [];
        foreach ($iterator as $value) {
            $keys = array();
            foreach (range(0, $iterator->getDepth()) as $depth) {
                $keys[] = $iterator->getSubIterator($depth)->key();
            }
            $dotKey = join('.', $keys);
            $encoded[$dotKey] = $value;
        }
        return $encoded;
    }

    /**
     * @param $encodedValue
     * @return array
     */
    public function decode($encodedValue): array
    {
        $decoded = new Dot();
        foreach ($encodedValue as $dotKey => $value) {
            $decoded->set($dotKey, $value);
        }
        return $decoded->all();
    }
}