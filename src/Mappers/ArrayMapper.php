<?php
/**
 * Created for DjinORM.
 * Datetime: 10.11.2017 15:17
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;

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
     * @param object $complex
     * @return array
     * @throws SerializerException
     */
    public function serialize($complex)
    {
        if (!is_array($complex)) {
            $type = gettype($complex);
            throw new SerializerException("Array can not be extracted from '{$type}' type");
        }

        if ($this->mapper) {
            return array_map(function ($data) {
                return $this->mapper->serialize($data);
            }, $complex);
        }

        return $complex;
    }

    /**
     * @param array $data
     * @return array
     * @throws SerializerException
     */
    public function deserialize($data)
    {
        if (!is_array($data)) {
            $type = gettype($data);
            throw new SerializerException("Array can not be hydrated from '{$type}' type");
        }

        if ($this->mapper) {
            return array_map(function ($data) {
                return $this->mapper->deserialize($data);
            }, $data);
        }

        return $data;
    }
}