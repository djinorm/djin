<?php
/**
 * Created for djin
 * Date: 08.03.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Repository\Storage;


use DjinORM\Djin\Manager\Commit;

interface StorageInterface
{

    /**
     * @param int|string $id
     * @return array|null
     */
    public function findById(string $id): ?array;

    /**
     * @param string[] $ids
     * @return array[]
     */
    public function findByIds($ids): array;

    /**
     * @param Commit $commit
     */
    public function commit(Commit $commit): void;

}