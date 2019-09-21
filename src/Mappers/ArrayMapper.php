<?php
/**
 * Created for DjinORM.
 * Datetime: 10.11.2017 15:17
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;

class ArrayMapper implements MapperInterface
{

    /**
     * @var MapperInterface|null
     */
    private $mapper;

    public function __construct(?MapperInterface $mapper = null)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param array $data
     * @return array
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        if (!is_array($data)) {
            $type = gettype($data);
            throw new HydratorException("Array can not be hydrated from '{$type}' type");
        }

        if ($this->mapper) {
            return array_map(function ($data) {
                return $this->mapper->hydrate($data);
            }, $data);
        }

        return $data;
    }

    /**
     * @param object $complex
     * @return array
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        if (!is_array($complex)) {
            $type = gettype($complex);
            throw new ExtractorException("Array can not be extracted from '{$type}' type");
        }

        if ($this->mapper) {
            return array_map(function ($data) {
                return $this->mapper->extract($data);
            }, $complex);
        }

        return $complex;
    }
}