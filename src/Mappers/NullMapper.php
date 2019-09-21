<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 07.09.2019 17:18
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

class NullMapper implements MapperInterface
{

    /**
     * @var MapperInterface
     */
    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return mixed
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        if ($data === null) {
            return null;
        }
        return $this->mapper->hydrate($data);
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return mixed
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        if (is_null($complex)) {
            return $complex;
        }
        return $this->mapper->extract($complex);
    }
}