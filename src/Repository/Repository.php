<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Exceptions\DuplicateModelException;
use DjinORM\Djin\Exceptions\NotPermanentIdException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;
use Throwable;

abstract class Repository
{

    /** @var ModelInterface[] */
    protected $registered;

    /**
     * @param Id|int|string $id
     * @param Throwable|null $notFoundException
     * @return ModelInterface|null
     */
    abstract public function findById($id, Throwable $notFoundException = null): ?ModelInterface;

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
    public function freeUpMemory(): void
    {
        $this->registered = [];
    }

    /**
     * @param ModelInterface $model
     * @throws DuplicateModelException
     * @throws NotPermanentIdException
     */
    protected function register(ModelInterface $model): void
    {
        if (!$model->getId()->isPermanent()) {
            throw new NotPermanentIdException('Model without permanent id can not be registered');
        }

        $class = get_class($model);
        $id = $model->getId()->toString();

        if (isset($this->registered[$id]) && $this->registered[$id] !== $model) {
            throw new DuplicateModelException("Model with class '{$class}' and id '{$id}' already registered");
        }

        $this->registered[$id] = $model;
    }

    protected function unregister(ModelInterface $model): void
    {
        if ($model->getId()->isPermanent()) {
            unset($this->registered[$model->getId()->toString()]);
        }
    }

}