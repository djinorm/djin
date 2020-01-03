<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;
use DjinORM\Djin\Id\Id;

class IdMapper implements MapperInterface
{

    /**
     * @param Id $complex
     * @return mixed
     * @throws SerializerException
     */
    public function serialize($complex)
    {
        if (!$complex->isPermanent()) {
            throw new SerializerException("Id should has assigned permanent value");
        }
        return $complex->toString();
    }

    /**
     * @inheritDoc
     */
    public function deserialize($data)
    {
        if (!is_string($data) && !is_int($data) && !is_float($data)) {
            throw new SerializerException("Id expected, but '" . gettype($data) . "' passed");
        }
        return new Id($data);
    }
}