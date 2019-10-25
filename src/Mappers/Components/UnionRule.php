<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 25.10.2019 13:35
 */

namespace DjinORM\Djin\Mappers\Components;


use DjinORM\Djin\Exceptions\SerializerException;
use DjinORM\Djin\Mappers\MapperInterface;

class UnionRule
{

    /**
     * @var MapperInterface
     */
    private $mapper;
    /**
     * @var callable
     */
    private $serialize;
    /**
     * @var callable
     */
    private $deserialize;

    public function __construct(MapperInterface $mapper, callable $serialize, callable $deserialize)
    {
        $this->mapper = $mapper;
        $this->serialize = $serialize;
        $this->deserialize = $deserialize;
    }

    /**
     * @param object $complex
     * @return array
     * @throws SerializerException
     */
    public function serialize($complex)
    {
        $serialized = $this->mapper->serialize($complex);
        return ($this->serialize)($complex, $serialized);
    }

    /**
     * @param mixed $serialized
     * @return object
     * @throws SerializerException
     */
    public function deserialize($serialized)
    {
        $handled = ($this->deserialize)($serialized);
        if ($handled) {
            return $this->mapper->deserialize($handled);
        }
        throw new SerializerException("Union rule mismatched");
    }

}