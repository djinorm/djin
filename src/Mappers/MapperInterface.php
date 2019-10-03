<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;

interface MapperInterface
{

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return mixed
     * @throws SerializerException
     */
    public function serialize($complex);

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return mixed
     * @throws SerializerException
     */
    public function deserialize($data);

}