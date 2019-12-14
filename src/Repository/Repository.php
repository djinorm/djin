<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;
use Throwable;

abstract class Repository
{

    /**
     * @param Id|int|string $id
     * @param Throwable|null $exception
     * @return ModelInterface|null
     */
    abstract public function findById($id, Throwable $exception = null): ?ModelInterface;

    /**
     * @param Id[]|array $ids
     * @return ModelInterface[]
     */
    abstract public function findByIds($ids): array;

    /**
     * @param Commit $commit
     */
    abstract public function commit(Commit $commit): void;

    /**
     * Освобождает из памяти загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     */
    abstract public function freeUpMemory(): void;

}