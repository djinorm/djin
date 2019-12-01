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

    /**
     * @inheritDoc
     */
    public function __invoke(ModelInterface $model): Id
    {
        if (!$model->getId()->isPermanent()) {
            $key = $this->prefix . ':' . $model::getModelName();
            $model->getId()->assign($this->redis->incr($key));
        }
        return $model->getId();
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