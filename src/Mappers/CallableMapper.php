<?php
/**
 * Created for djin
 * Datetime: 01.10.2019 12:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use Exception;

class CallableMapper implements MapperInterface
{

    /**
     * @var callable
     */
    private $hydrate;
    /**
     * @var callable
     */
    private $extract;

    public function __construct(callable $hydrate, callable $extract)
    {
        $this->hydrate = $hydrate;
        $this->extract = $extract;
    }

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param mixed $data
     * @return mixed
     * @throws HydratorException
     */
    public function hydrate($data)
    {
        try {
            return ($this->hydrate)($data);
        } catch (Exception $exception) {
            throw new HydratorException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param $complex
     * @return mixed
     * @throws ExtractorException
     */
    public function extract($complex)
    {
        try {
            return ($this->extract)($complex);
        } catch (Exception $exception) {
            throw new ExtractorException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}