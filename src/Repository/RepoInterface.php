<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;

interface RepoInterface
{

    /**
     * @param $id
     * @return ModelInterface|null
     */
    public function findById($id): ?ModelInterface;

    /**
     * @param ModelInterface $model
     * @return mixed|void
     */
    public function save(ModelInterface $model);

    /**
     * @param ModelInterface $model
     * @return mixed|void
     */
    public function insert(ModelInterface $model);

    /**
     * @param ModelInterface $model
     * @return mixed|void
     */
    public function update(ModelInterface $model);

    /**
     * @param ModelInterface $model
     * @return mixed|void
     */
    public function delete(ModelInterface $model);

    /**
     * @param ModelInterface $model
     * @return Id
     */
    public function setPermanentId(ModelInterface $model): Id;

    /**
     * Освобождает из памяти загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     * @return mixed|void
     */
    public function freeUpMemory();

    /**
     * @return string
     */
    public static function getModelClass(): string;
}