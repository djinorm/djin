<?php


namespace DjinORM\Djin\Locker;


use DjinORM\Djin\Model\Link;
use DjinORM\Djin\Model\ModelInterface;

class DummyLocker implements LockerInterface
{

    public function lock(ModelInterface $model, ModelInterface $locker, int $timeout = null): bool
    {
        return true;
    }

    public function unlock(ModelInterface $model, ModelInterface $locker): bool
    {
        return true;
    }

    public function passLock(ModelInterface $model, ModelInterface $currentLocker, ModelInterface $nextLocker, int $timeout): bool
    {
        return true;
    }

    public function isLockedFor(ModelInterface $model, ?ModelInterface $locker): bool
    {
        return false;
    }

    public function getDefaultTimeout(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getLocker($modelOrLink): ?Link
    {
        return null;
    }
}