<?php


namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;
use Throwable;

class Repository extends \DjinORM\Djin\Repository\Repository
{

    /**
     * @inheritDoc
     */
    public function findById($id, Throwable $notFoundException = null): ?ModelInterface
    {
        // TODO: Implement findById() method.
    }

    /**
     * @inheritDoc
     */
    public function findByIds($ids): array
    {
        // TODO: Implement findByIds() method.
    }

    /**
     * @inheritDoc
     */
    public function commit(Commit $commit): void
    {
        // TODO: Implement commit() method.
    }

    /**
     * @inheritDoc
     */
    public function freeUpMemory(): void
    {
        // TODO: Implement freeUpMemory() method.
    }
}