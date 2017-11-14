<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:12
 */

namespace DjinORM\Djin\Manager;

use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\StubModelInterface;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Repository\RepositoryInterface;

class ModelManager
{
    /** @var ModelInterface[] */
    protected $models = [];

    /** @var  ModelInterface[] */
    protected $modelsToDelete = [];

    /** @var IdGeneratorInterface  */
    protected $idGenerator;

    /** @var  ModelConfig[] */
    protected $config = [];

    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param $modelClassOrObject
     * @return ModelConfig
     * @throws UnknownModelException
     */
    public function getModelConfig($modelClassOrObject): ModelConfig
    {
        $class = is_object($modelClassOrObject) ? get_class($modelClassOrObject) : $modelClassOrObject;
        if (!isset($this->config[$class])) {
            throw new UnknownModelException('No configuration for model ' . $class);
        }
        return $this->config[$class];
    }

    /**
     * @param RepositoryInterface $repository
     * @param string|array|null $modelClass имя класса модели, массив имен классов моделей или null,
     *        чтобы модель была взята из репозитория
     * @param IdGeneratorInterface|null $idGenerator
     */
    public function setModelConfig(RepositoryInterface $repository, $modelClass = null, IdGeneratorInterface $idGenerator = null)
    {
        if ($idGenerator === null) {
            $idGenerator = $this->idGenerator;
        }

        if (is_array($modelClass)) {
            foreach ($modelClass as $class) {
                $this->config[$class] = new ModelConfig($repository, $idGenerator);
            }
        } else {
            $this->config[$modelClass ?? $repository::getModelClass()] = new ModelConfig($repository, $idGenerator);
        }
    }

    /**
     * @param $modelClassOrObject
     * @return RepositoryInterface
     * @throws UnknownModelException
     */
    public function getModelRepository($modelClassOrObject): RepositoryInterface
    {
        return $this->getModelConfig($modelClassOrObject)->getRepository();
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
            $this->models[$model->getId()->getTempId()] = $model;
        }

        return count($this->models);
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
        $result = [];
        foreach ($this->models as $model) {
            $result[get_class($model)][] = $model;
        }
        return $result;
    }

    public function isPersistedModel(ModelInterface $model): bool
    {
        foreach ($this->models as $persistedModel) {
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
            return count($this->modelsToDelete);
        }

        if (self::isNewModel($modelToDelete)) {
            unset($this->models[$modelToDelete->getId()->getTempId()]);
        } else {
            $this->modelsToDelete[$modelToDelete->getId()->getTempId()] = $modelToDelete;
        }

        return count($this->modelsToDelete);
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
        $result = [];
        foreach ($this->modelsToDelete as $model) {
            $result[get_class($model)][] = $model;
        }
        return $result;
    }

    public function isPreparedToDeleteModel(ModelInterface $model): bool
    {
        foreach ($this->modelsToDelete as $modelToDelete) {
            if ($modelToDelete === $model) {
                return true;
            }
        }
        return false;
    }


    /**
     * @throws UnknownModelException
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function commit()
    {
        foreach ($this->models as $model) {
            $this->setPermanentId($model);
        }

        //Сначала удаляем данные только из транзакционных репозиториев
        foreach ($this->modelsToDelete as $key => $model) {
            $repository = $this->getModelConfig($model)->getRepository();
            if (!$repository->isTransactional()) {
                continue;
            }
            $repository->delete($model);
            unset($this->modelsToDelete[$key]);
        }

        //И коммитим только из транзакционных репозиториев
        foreach ($this->models as $key => $model) {
            $repository = $this->getModelConfig($model)->getRepository();
            if (!$repository->isTransactional()) {
                continue;
            }
            $repository->save($model);
            unset($this->models[$key]);
        }

        //Потом удялем из не транзакционных репозитореив
        foreach ($this->modelsToDelete as $key => $model) {
            $repository = $this->getModelConfig($model)->getRepository();
            $repository->delete($model);
            unset($this->modelsToDelete[$key]);
        }

        //И коммитим из не транзакционных репозиториев
        foreach ($this->models as $key => $model) {
            $repository = $this->getModelConfig($model)->getRepository();
            $repository->save($model);
            unset($this->models[$key]);
        }
    }

    public function getTotalQueryCount()
    {
        $count = 0;
        foreach ($this->config as $config) {
            $count += $config->getRepository()->getQueryCount();
        }
        return $count;
    }

    /**
     * Освобождает из памяти всех репозиториев загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     */
    public function freeUpMemory()
    {
        foreach ($this->config as $config) {
            $config->getRepository()->freeUpMemory();
        }
    }

    /**
     * @param ModelInterface $model
     * @throws UnknownModelException
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    protected function setPermanentId(ModelInterface $model)
    {
        if (self::isNewModel($model)) {
            $nextId = $this->getModelConfig($model)->getIdGenerator()->getNextId($model);
            $model->getId()->setPermanentId($nextId);
        }
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