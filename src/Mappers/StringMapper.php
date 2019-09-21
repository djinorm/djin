<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

class StringMapper  implements MapperInterface
{

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return string
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        if (!is_scalar($data)) {
            $type = gettype($data);
            throw new HydratorException("String can not be hydrated from '{$type}' type");
        }

        return (string) $data;
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return string
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        if (!is_scalar($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("String can not be extracted from '{$type}' type");
        }
        return (string) $complex;
    }
}