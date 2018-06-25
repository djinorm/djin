<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:12
 */

namespace DjinORM\Djin\Manager;

use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\StubModelInterface;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Repository\RepositoryInterface;
use Psr\Container\ContainerInterface;

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

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getConfig(): array
    {
        return $this->modelRepositories;
    }

    /**
     * @param $modelClassOrObject
     * @return RepositoryInterface
     * @throws UnknownModelException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * Подготавливает модели для будущего сохранения в базу
     * @param ModelInterface[] $models
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
            $tempId = $model->getId()->getTempId();

            $this->models[$class][$tempId] = $model;
        }

        return $this->getModelsCount($this->models);
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
     * Подготавливает модели для будущего сохранения в базу
     * @param ModelInterface $modelToDelete
     * @return int общее число подготовленных для удаления моделей
     */
    public function delete(ModelInterface $modelToDelete): int
    {
        if ($modelToDelete instanceof StubModelInterface) {
            return $this->getModelsCount($this->modelsToDelete);
        }

        $class = get_class($modelToDelete);
        $tempId = $modelToDelete->getId()->getTempId();

        unset($this->models[$class][$tempId]);
        $this->modelsToDelete[$class][$tempId] = $modelToDelete;

        return $this->getModelsCount($this->modelsToDelete);
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
     * @throws UnknownModelException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function commit()
    {
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
    }

    /**
     * Освобождает из памяти всех репозиториев загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     *
     * @throws UnknownModelException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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