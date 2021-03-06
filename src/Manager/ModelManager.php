<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:12
 */

namespace DjinORM\Djin\Manager;

use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Relation;
use DjinORM\Djin\Model\StubModelInterface;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Repository\RepositoryInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ModelManager
{
    /** @var ModelInterface[][] */
    protected $models = [];

    /** @var ModelInterface[][] */
    protected $modelsToDelete = [];

    /** @var array */
    protected $modelRepositories = [];

    /** @var ContainerInterface */
    private $container;

    /** @var callable */
    protected $onBeforeCommit;

    /** @var callable */
    protected $onAfterCommit;

    /** @var callable */
    private $onCommitException;

    public function __construct(
        ContainerInterface $container,
        callable $onBeforeCommit = null,
        callable $onAfterCommit = null,
        callable $onCommitException = null
    )
    {
        $this->container = $container;
        $this->onBeforeCommit = $onBeforeCommit;
        $this->onAfterCommit = $onAfterCommit;
        $this->onCommitException = $onCommitException;
    }

    public function getConfig(): array
    {
        return $this->modelRepositories;
    }

    /**
     * @param $modelClassOrObject
     * @return RepositoryInterface
     * @throws UnknownModelException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getModelRepository($modelClassOrObject): RepositoryInterface
    {
        $class = is_object($modelClassOrObject) ? get_class($modelClassOrObject) : $modelClassOrObject;
        if (!isset($this->modelRepositories[$class])) {
            throw new UnknownModelException('No repository for model ' . $class);
        }
        return $this->container->get($this->modelRepositories[$class]);
    }

    /**
     * @param string $modelName
     * @return RepositoryInterface
     * @throws UnknownModelException
     */
    public function getRepositoryByModelName($modelName): RepositoryInterface
    {
        foreach ($this->modelRepositories as $modelClass => $modelRepoClass) {
            if (call_user_func([$modelClass, 'getModelName']) == $modelName) {
                return $this->container->get($modelRepoClass);
            }
        }
        throw new UnknownModelException('No repository for model with name ' . $modelName);
    }

    /**
     * @param string $repositoryClass
     * @param string|array|null $modelClass имя класса модели, массив имен классов моделей или null, чтобы модель была взята из репозитория
     */
    public function setModelRepository(string $repositoryClass, $modelClass = null)
    {
        if (is_array($modelClass)) {
            foreach ($modelClass as $class) {
                $this->modelRepositories[$class] = $repositoryClass;
            }
        } else {
            $this->modelRepositories[$modelClass ?? call_user_func($repositoryClass . '::getModelClass')] = $repositoryClass;
        }
    }

    /**
     * @param Relation $relation
     * @return ModelInterface|null
     * @throws UnknownModelException
     */
    public function findRelation(Relation $relation): ?ModelInterface
    {
        $repo = $this->getRepositoryByModelName($relation->getModelName());
        return $repo->findById($relation->getId());
    }

    /**
     * Подготавливает модели для будущего сохранения в базу
     * @param ModelInterface|ModelInterface[] $models
     * @return int общее число подготовленных для сохранения моделей
     * @throws NotModelInterfaceException
     */
    public function persists($models = []): int
    {
        if (!is_array($models)) {
            $models = func_get_args();
        }

        foreach ($models as $model) {

            if ($model instanceof StubModelInterface) {
                continue;
            }

            $this->guardNotModelInterface($model);

            $class = get_class($model);
            $hash = spl_object_hash($model);

            $this->models[$class][$hash] = $model;
        }

        return $this->getModelsCount($this->models);
    }

    public function resetPersisted()
    {
        $this->models = [];
    }

    /**
     * Возвращает массив моделей, подготовленных для сохранения в следующем формате
     * [
     *      '\namespace\User' => [
     *          $userModel_1,
     *          $userModel_2
     *      ],
     *      '\namespace\Profile' => [
     *          $profileModel_1,
     *          $profileModel_2
     *      ]
     * ]
     *
     * @return array
     */
    public function getPersistedModels(): array
    {
        return array_filter(
            array_map('array_values', $this->models)
        );
    }

    public function isPersistedModel(ModelInterface $model): bool
    {
        foreach ($this->models[get_class($model)] as $persistedModel) {
            if ($persistedModel === $model) {
                return true;
            }
        }
        return false;
    }


    /**
     * Подготавливает модели для будущего удаления из базы
     * @param ModelInterface|ModelInterface[] $models
     * @return int общее число подготовленных для удаления моделей
     * @throws NotModelInterfaceException
     */
    public function delete($models = []): int
    {
        if (!is_array($models)) {
            $models = func_get_args();
        }

        foreach ($models as $model) {
            if ($model instanceof StubModelInterface) {
                return $this->getModelsCount($this->modelsToDelete);
            }

            $this->guardNotModelInterface($model);

            $class = get_class($model);
            $hash = spl_object_hash($model);

            unset($this->models[$class][$hash]);
            $this->modelsToDelete[$class][$hash] = $model;
        }

        return $this->getModelsCount($this->modelsToDelete);
    }

    public function resetDeleted()
    {
        $this->modelsToDelete = [];
    }

    /**
     * Возвращает массив моделей, подготовленных для удаления в следующем формате
     * [
     *      '\namespace\User' => [
     *          $userModel_1,
     *          $userModel_2
     *      ],
     *      '\namespace\Profile' => [
     *          $profileModel_1,
     *          $profileModel_2
     *      ]
     * ]
     *
     * @return array
     */
    public function getPreparedToDeleteModels(): array
    {
        return array_filter(
            array_map('array_values', $this->modelsToDelete)
        );
    }

    public function isPreparedToDeleteModel(ModelInterface $model): bool
    {
        $models = $this->modelsToDelete[get_class($model)];
        foreach ($models as $modelToDelete) {
            if ($modelToDelete === $model) {
                return true;
            }
        }
        return false;
    }


    /**
     * @throws Exception
     */
    public function commit()
    {
        $modelsToSave = array_map('array_values', $this->models);
        $modelsToDelete = array_map('array_values', $this->modelsToDelete);

        if ($this->onBeforeCommit) {
            $beforeCommitCallback = $this->onBeforeCommit;
            $beforeCommitCallback($this, $modelsToSave, $modelsToDelete);
        }

        try {
            foreach ($this->models as $modelClass => $models) {
                foreach ($models as $model) {
                    $this->getModelRepository($model)->setPermanentId($model);
                }
            }

            foreach ($this->modelsToDelete as $modelClass => $models) {
                foreach ($models as $id => $model) {
                    $this->getModelRepository($model)->delete($model);
                    unset($this->modelsToDelete[$modelClass][$id]);
                }
            }

            foreach ($this->models as $modelClass => $models) {
                foreach ($models as $id => $model) {
                    $this->getModelRepository($model)->save($model);
                    unset($this->models[$modelClass][$id]);
                }
            }

            if ($this->onAfterCommit) {
                $afterCommitCallback = $this->onAfterCommit;
                $afterCommitCallback($this, $modelsToSave, $modelsToDelete);
            }

        } catch (Exception $exception) {
            if ($this->onCommitException) {
                $commitExceptionCallback = $this->onCommitException;
                $commitExceptionCallback($this, $modelsToSave, $modelsToDelete);
            }
            throw $exception;
        }

    }

    /**
     * Освобождает из памяти всех репозиториев загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     *
     * @throws UnknownModelException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function freeUpMemory()
    {
        foreach (array_keys($this->modelRepositories) as $modelClass) {
            $this->getModelRepository($modelClass)->freeUpMemory();
        }
    }

    private function getModelsCount(array $classToModelArray): int
    {
        return array_sum(
            array_map('count', $classToModelArray)
        );
    }

    /**
     * @param $model
     * @throws NotModelInterfaceException
     */
    private function guardNotModelInterface($model)
    {
        if (!$model instanceof ModelInterface) {
            throw new NotModelInterfaceException();
        }
    }

    public static function isNewModel(ModelInterface $model)
    {
        return !$model->getId()->isPermanent();
    }

}