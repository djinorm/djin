<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

class FloatMapper implements MapperInterface
{

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return float
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        if (!is_numeric($data) && !is_string($data)) {
            $type = gettype($data);
            throw new HydratorException("Float can not be hydrated from '{$type}' type");
        }

        return (float) $data;
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return float
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        if (!is_numeric($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("Float can not be extracted from '{$type}' type");
        }
        return (float) $complex;
    }
}