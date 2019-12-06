<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;

interface RepoInterface
{

    /**
     * @param mixed $id
     * @return ModelInterface|null
     */
    public function findById($id): ?ModelInterface;

    /**
     * @param array $ids
     * @return ModelInterface[]
     */
    public function findByIds($ids): array;

    /**
     * @param Commit $commit
     * @return Id|null return some commit/transaction Id. Usable for cross-database transactions or two-phase commits
     */
    public function commit(Commit $commit): ?Id;

    /**
     * Освобождает из памяти загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     */
    public function freeUpMemory(): void;

}