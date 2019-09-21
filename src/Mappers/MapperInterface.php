<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

interface MapperInterface
{

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return mixed
     * @throws HydratorException
     */
    public function hydrate($data);

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return mixed
     * @throws ExtractorException
     */
    public function extract($complex);

}