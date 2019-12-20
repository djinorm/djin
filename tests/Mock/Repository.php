<?php

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Manager\Commit;
use DjinORM\Djin\Model\ModelInterface;
use Throwable;

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
    public function findById($id, Throwable $notFoundException = null): ?ModelInterface
    {
        $model = $this->models[(string) $id] ?? null;
        if (is_null($model) && $notFoundException) {
            throw $notFoundException;
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
            $this->models[$model->getId()->toString()] = $model;
        }

        $deletedModels = $commit->getDeleted($this->getClassName());
        foreach ($deletedModels as $model) {
            unset($this->models[$model->getId()->toString()]);
        }
    }

    abstract protected function getClassName(): string;

}