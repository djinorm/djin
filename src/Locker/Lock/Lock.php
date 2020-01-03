<?php
/**
 * Created for djin
 * Date: 02.01.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Locker\Lock;


use DjinORM\Djin\Model\ModelInterface;

class Lock
{

    /** @var ModelInterface */
    private $locker;

    /** @var int|null */
    private $timeout;

    public function __construct(ModelInterface $locker, int $timeout = null)
    {
        $this->locker = $locker;
        $this->timeout = $timeout;
    }

    /**
     * @return ModelInterface
     */
    public function getLocker(): ModelInterface
    {
        return $this->locker;
    }

    /**
     * @return int|null
     */
    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

}