<?php


namespace DjinORM\Djin\Locker;


use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Link;

interface LockerInterface
{

    public function lock(ModelInterface $model, ModelInterface $locker, int $timeout = null): bool;

    public function unlock(ModelInterface $model, ModelInterface $locker): bool;

    public function passLock(ModelInterface $model, ModelInterface $currentLocker, ModelInterface $nextLocker, int $timeout): bool;

    public function isLockedFor(ModelInterface $model, ?ModelInterface $locker): bool;

    public function getDefaultTimeout(): int;

    /**
     * @param ModelInterface|Link $modelOrLink
     * @return Link|null
     */
    public function getLocker($modelOrLink): ?Link;

}
