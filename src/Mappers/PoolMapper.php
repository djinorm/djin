<?php
/**
 * Created for djin
 * Date: 02.01.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;
use DjinORM\Djin\Pool\PoolInterface;

class PoolMapper implements MapperInterface
{

    /**
     * @var string
     */
    private $poolClass;
    /**
     * @var MapperInterface
     */
    private $mapper;

    public function __construct(string $poolClass, MapperInterface $mapper)
    {
        $this->poolClass = $poolClass;
        $this->mapper = $mapper;
    }

    /**
     * @param PoolInterface $complex
     * @return array|mixed
     * @throws SerializerException
     */
    public function serialize($complex)
    {
        if (!is_a($complex, $this->poolClass)) {
            $type = gettype($complex);
            throw new SerializerException("'{$this->poolClass}' expected, but '{$type}' type passed");
        }

        return [
            'current' => $this->mapper->serialize($complex->getCurrent()),
            'pool' => (new NullOrMapper($this->mapper))->serialize($complex->getPool())
        ];
    }

    /**
     * @inheritDoc
     */
    public function deserialize($data)
    {
        $current = $this->mapper->deserialize($data);

        $class = $this->poolClass;

        /** @var PoolInterface $pool */
        return new $class($current);
    }

}