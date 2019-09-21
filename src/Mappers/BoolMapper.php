<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 11:28
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

class BoolMapper implements MapperInterface
{

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return bool
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        if (!is_scalar($data)) {
            $type = gettype($data);
            throw new HydratorException("Bool can not be hydrated from '{$type}' type");
        }

        return (bool) $data;
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return bool
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        if (!is_bool($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("Can not extract bool from '{$type}' type");
        }
        return (bool) $complex;
    }

}