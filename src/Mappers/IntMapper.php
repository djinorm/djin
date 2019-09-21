<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

class IntMapper implements MapperInterface
{

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return int
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        if (!is_int($data) && !is_string($data)) {
            $type = gettype($data);
            throw new HydratorException("Integer can not be hydrated from '{$type}' type");
        }

        return (int) $data;
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return int
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        if (!is_int($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("Integer can not be extracted from '{$type}' type");
        }
        return (int) $complex;
    }
}