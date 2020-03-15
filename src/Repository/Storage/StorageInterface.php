<?php
/**
 * Created for djin
 * Date: 08.03.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Repository\Storage;


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

    public function insert(string $id, array $data): void;

    public function update(string $id, array $data): void;

    public function delete(string $id): void;

}