<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 16:29
 */

namespace DjinORM\Djin\Id;

use DjinORM\Djin\Model\ModelInterface;
use Redis;

class RedisIdGenerator implements IdGeneratorInterface
{

    private $prefix;
    private $redis;

    public function __construct(Redis $redis, $prefix = 'RedisIdGenerator')
    {
        $this->prefix = $prefix;
        $this->redis = $redis;
    }

    public function getNextId(ModelInterface $model)
    {
        $key = $this->prefix . ':' . $model::getModelName();
        return $this->redis->incr($key);
    }

    /**
     * @param string $modelName
     * @param int $current
     */
    public function setCounterValue(string $modelName, int $current)
    {
        $key = $this->prefix . ':' . $modelName;
        $this->redis->set($key, $current);
    }
}