<?php
/**
 * Created for DjinORM.
 * Datetime: 18.07.2018 14:00
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;


use Adbar\Dot;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class CustomCharNotation implements NotationInterface
{

    /**
     * @var string
     */
    private $char;

    public function __construct(string $char = '.')
    {
        $this->char = $char;
    }

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
            $dotKey = join($this->char, $keys);
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
            $dotKey = str_replace($this->char, '.', $dotKey);
            $decoded->set($dotKey, $value);
        }
        return $decoded->all();
    }

    public function isDecodeFirst(): bool
    {
        return true;
    }
}