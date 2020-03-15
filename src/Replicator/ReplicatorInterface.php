<?php
/**
 * Created for djin
 * Date: 08.03.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Replicator;


use DjinORM\Djin\Repository\Storage\StorageInterface;

interface ReplicatorInterface
{

    public function __construct(array $storages);

    /**
     * Should return storage by name or return primary storage if name is null
     * @param string|null $name
     * @return StorageInterface
     */
    public function getStorage(string $name = null): StorageInterface;

    public function insert(string $id, array $data): void;

    public function update(string $id, array $data): void;

    public function delete(string $id): void;

}