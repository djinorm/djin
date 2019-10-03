<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 11:28
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;

class BoolMapper implements MapperInterface
{

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return bool
     * @throws SerializerException
     */
    public function serialize($complex)
    {
        if (!is_bool($complex)) {
            $type = gettype($complex);
            throw new SerializerException("Can not serialize bool from '{$type}' type");
        }
        return (bool) $complex;
    }

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return bool
     * @throws SerializerException
     */
    public function deserialize($data)
    {
        if (!is_scalar($data)) {
            $type = gettype($data);
            throw new SerializerException("Bool can not be hydrated from '{$type}' type");
        }

        return (bool) $data;
    }

}