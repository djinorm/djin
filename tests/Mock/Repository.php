<?php

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;
use Exception;

abstract class Repository extends \DjinORM\Djin\Repository\Repository
{

    /** @var ModelInterface */
    private $models;

    public function __construct()
    {
        $class = $this->getClassName();
        $this->models = [
            '1' => new $class(1),
            '2' => new $class(2),
            '3' => new $class(3),
        ];
    }

    /**
     * @inheritDoc
     */
    public function findById($id, Exception $notFoundException = null): ?ModelInterface
    {
        $model = $this->models[(string) $id] ?? null;
        if (is_null($model) && $notFoundException) {
            throw $notFoundException;
        }

        if ($model) {
            $this->register($model);
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function findByIds($ids): array
    {
        $models = [];
        foreach ($ids as $id) {
            if ($model = $this->findById($id)) {
                $this->register($model);
                $models[] = $model;
            }
        }
        return $models;
    }

    /**
     * @inheritDoc
     */
    public function commit(Commit $commit): void
    {
        $persistedModels = $commit->getPersisted($this->getClassName());
        foreach ($persistedModels as $model) {
            $this->modelException($model);
            $this->models[(string) $model->id()] = $model;
            $this->register($model);
        }

        $deletedModels = $commit->getDeleted($this->getClassName());
        foreach ($deletedModels as $model) {
            $this->modelException($model);
            unset($this->models[(string) $model->id()]);
            $this->unregister($model);
        }
    }

    public function getRegistered(): array
    {
        return $this->registered;
    }

    /**
     * @param ModelInterface $model
     * @throws Exception
     */
    protected function modelException(ModelInterface $model)
    {
        if ($model->id()->isEqual('exception')) {
            throw new Exception('Some repository exception');
        }
    }

    protected function hydrate(array $data): ModelInterface
    {
        // TODO: Implement hydrate() method.
    }

    protected function extract(ModelInterface $model): array
    {
        // TODO: Implement extract() method.
    }

    abstract protected function getClassName(): string;

}