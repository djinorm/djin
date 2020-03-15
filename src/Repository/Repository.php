<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:10
 */

namespace DjinORM\Djin\Repository;

use DjinORM\Djin\Exceptions\DuplicateModelException;
use DjinORM\Djin\Exceptions\NotPermanentIdException;
use DjinORM\Djin\Helpers\IdHelper;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Replicator\ReplicatorInterface;
use Exception;

abstract class Repository
{

    /** @var ModelInterface[] */
    protected $registered;

    /** @var ReplicatorInterface */
    private $replicator;

    public function __construct(ReplicatorInterface $replicator)
    {
        $this->replicator = $replicator;
    }

    /**
     * @param Id|int|string $id
     * @param Exception|null $notFoundException
     * @return ModelInterface|null
     * @throws Exception
     */
    public function findById($id, Exception $notFoundException = null): ?ModelInterface
    {
        $storage = $this->replicator->getStorage();
        $data = $storage->findById((string) $id);
        if (is_null($data)) {
            throw $notFoundException;
        }
        return $this->populateOne($data);
    }

    /**
     * @param Id[]|array $ids
     * @return ModelInterface[]
     */
    public function findByIds($ids): array
    {
        $storage = $this->replicator->getStorage();
        $array = $storage->findByIds(IdHelper::scalarizeMany($ids));
        return $this->populateMany($array);
    }

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
     * @return array
     */
    abstract protected function extract(ModelInterface $model): array;

    /**
     * @param array $data
     * @return ModelInterface
     */
    abstract protected function hydrate(array $data): ModelInterface;

    /**
     * @param array $data
     * @return ModelInterface
     */
    protected function populateOne(array $data): ModelInterface
    {
        $model = $this->hydrate($data);

        if ($registered = $this->registered[(string) $model->getId()] ?? null) {
            return $registered;
        }

        $this->register($model);
        return $model;
    }


    /**
     * @param array[] $array
     * @return ModelInterface[]
     */
    protected function populateMany(array $array): array
    {
        $models = [];
        foreach ($array as $key => $data) {
            $models[$key] = $this->populateOne($data);
        }
        return $models;
    }

    /**
     * @param ModelInterface $model
     * @return bool
     */
    protected function isRegistered(ModelInterface $model): bool
    {
        return isset($this->registered[(string) $model->getId()]);
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
        $id = (string) $model->getId();

        if (isset($this->registered[$id]) && $this->registered[$id] !== $model) {
            throw new DuplicateModelException("Model with class '{$class}' and id '{$id}' already registered");
        }

        $this->registered[$id] = $model;
    }

    protected function unregister(ModelInterface $model): void
    {
        if ($model->getId()->isPermanent()) {
            unset($this->registered[(string) $model->getId()]);
        }
    }

}