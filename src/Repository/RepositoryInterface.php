<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Manager\ModelManager;
use DjinORM\Djin\Model\ModelInterface;

interface RepositoryInterface
{

    /**
     * Сообщает, может ли репозиторий откатить изменения. Если да, то
     * @see ModelManager сохранит эту модель одной из первых
     * @return bool
     */
    public function isTransactional(): bool;

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
    public function findByIds(array $ids):array;


    public function save(ModelInterface $model);
    public function insert(ModelInterface $model);
    public function update(ModelInterface $model);
    public function delete(ModelInterface $model);

    public function getQueryCount():int;

    /**
     * Освобождает из памяти загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     */
    public function freeUpMemory();

    public static function getModelClass(): ?string;
}