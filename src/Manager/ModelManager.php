<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:12
 */

namespace DjinORM\Djin\Manager;

use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Relation;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Repository\RepoInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ModelManager
{
    /**
     * @var ModelInterface[]
     */
    protected $persisted = [];
    /**
     * @var ModelInterface[]
     */
    protected $deleted = [];
    /**
     * @var array
     */
    protected $modelRepositories = [];
    /**
     * @var IdGeneratorInterface
     */
    private $modelIdGenerators;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var callable
     */
    protected $onBeforeCommit;
    /**
     * @var callable
     */
    protected $onAfterCommit;
    /**
     * @var callable
     */
    private $onCommitException;
    /**
     * @var IdGeneratorInterface
     */
    private $idGenerator;

    public function __construct(
        ContainerInterface $container,
        IdGeneratorInterface $idGenerator,
        callable $onBeforeCommit = null,
        callable $onAfterCommit = null,
        callable $onCommitException = null
    )
    {
        $this->container = $container;
        $this->idGenerator = $idGenerator;

        $this->onBeforeCommit = $onBeforeCommit ?? function () {};
        $this->onAfterCommit = $onAfterCommit ?? function () {};
        $this->onCommitException = $onCommitException ?? function () {};
    }

    /**
     * @param $modelClassOrObject
     * @return RepoInterface
     * @throws UnknownModelException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getModelRepository($modelClassOrObject): RepoInterface
    {
        $class = is_object($modelClassOrObject) ? get_class($modelClassOrObject) : $modelClassOrObject;
        if (!isset($this->modelRepositories[$class])) {
            throw new UnknownModelException('No repository for model ' . $class);
        }
        return $this->container->get($this->modelRepositories[$class]);
    }

    /**
     * @param string $modelName
     * @return RepoInterface
     * @throws UnknownModelException
     */
    public function getRepositoryByModelName($modelName): RepoInterface
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
     * @param array $modelClasses
     * @param IdGeneratorInterface|null $idGenerator
     */
    public function setModelConfig(string $repositoryClass, array $modelClasses, IdGeneratorInterface $idGenerator = null)
    {
        foreach ($modelClasses as $class) {
            $this->modelRepositories[$class] = $repositoryClass;
            $this->modelIdGenerators[$class] = $idGenerator ?? $this->idGenerator;
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
     * @throws NotModelInterfaceException
     */
    public function persists($models = []): void
    {
        if (!is_array($models)) {
            $models = func_get_args();
        }

        foreach ($models as $model) {
            $this->guardNotModelInterface($model);
            $hash = spl_object_hash($model);
            $this->persisted[$hash] = $model;
            unset($this->deleted[$hash]);
        }
    }

    public function resetPersisted(): void
    {
        $this->persisted = [];
    }

    /**
     * Подготавливает модели для будущего удаления из базы
     * @param ModelInterface|ModelInterface[] $models
     * @throws NotModelInterfaceException
     */
    public function delete($models = []): void
    {
        if (!is_array($models)) {
            $models = func_get_args();
        }

        foreach ($models as $model) {
            $this->guardNotModelInterface($model);
            $hash = spl_object_hash($model);
            $this->deleted[$hash] = $model;
            unset($this->persisted[$hash]);
        }
    }

    public function resetDeleted(): void
    {
        $this->deleted = [];
    }

    /**
     * @throws Exception
     */
    public function commit(): void
    {
        $commit = new Commit($this->persisted, $this->deleted);

        //Assigns real ids
        foreach ($commit->getPersisted() as $model) {
            ($this->idGenerator)($model);
        }

        ($this->onBeforeCommit)($this, $commit);

        try {

            $modelClasses = array_unique(array_map(
                'get_class',
                array_merge($commit->getPersisted(), $commit->getDeleted())
            ));

            foreach ($modelClasses as $modelClass) {
                $this->getModelRepository($modelClass)->commit($commit);
            }

            ($this->onAfterCommit)($this, $commit);

        } catch (Exception $exception) {
            ($this->onCommitException)($this, $commit);
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

    /**
     * @param $model
     * @throws NotModelInterfaceException
     */
    private function guardNotModelInterface($model)
    {
        if (!$model instanceof ModelInterface) {
            throw new NotModelInterfaceException($model);
        }
    }

    public static function isNewModel(ModelInterface $model)
    {
        return !$model->getId()->isPermanent();
    }

}