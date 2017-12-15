<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;

interface RepositoryInterface
{

    /**
     * @param $id
     * @return ModelInterface|null
     */
    public function findById($id);

    /**
     * @param $id
     * @param \Exception|null $exception
     * @return ModelInterface|null
     */
    public function findByIdOrException($id, \Exception $exception = null);

    /**
     * @param array $ids
     * @return ModelInterface[]
     */
    public function findByIds(array $ids): array;


    public function save(ModelInterface $model);
    public function insert(ModelInterface $model);
    public function update(ModelInterface $model);
    public function delete(ModelInterface $model);

    public function setPermanentId(ModelInterface $model): Id;
    public function getQueryCount(): int;

    public function onCommit();
    public function onRollback();

    /**
     * Освобождает из памяти загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     */
    public function freeUpMemory();

    public static function getModelClass(): ?string;
}