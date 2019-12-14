<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:12
 */

namespace DjinORM\Djin\Manager;

use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LockedModelException;
use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Locker\LockerInterface;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Link;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Repository\RepoInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SplObjectStorage;
use Throwable;

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
     * @var RepositoryManager
     */
    private $repositoryManager;
    /**
     * @var RepoInterface[]
     */
    private $repositories;
    /**
     * @var IdGeneratorInterface
     */
    private $IdGenerators;
    /**
     * @var IdGeneratorInterface
     */
    private $idGenerator;
    /**
     * @var LockerInterface
     */
    private $locker;
    /**
     * @var callable
     */
    private $onBeforeCommit;
    /**
     * @var callable
     */
    private $onAfterCommit;
    /**
     * @var callable
     */
    private $onCommitException;

    public function __construct(
        IdGeneratorInterface $idGenerator,
        LockerInterface $locker,
        callable $onBeforeCommit = null,
        callable $onAfterCommit = null,
        callable $onCommitException = null
    )
    {
        $this->repositoryManager = new RepositoryManager();
        $this->idGenerator = $idGenerator;
        $this->locker = $locker;

        $this->onBeforeCommit = $onBeforeCommit ?? function () {};
        $this->onAfterCommit = $onAfterCommit ?? function () {};
        $this->onCommitException = $onCommitException ?? function () {};
    }

    /**
     * @param RepoInterface|callable $repoOrCallable
     * @param array $modelClasses
     * @param IdGeneratorInterface|null $idGenerator
     */
    public function setModelConfig($repoOrCallable, array $modelClasses, IdGeneratorInterface $idGenerator = null)
    {
        foreach ($modelClasses as $modelClass) {
            $this->repositoryManager->add($repoOrCallable, $modelClass);
            $this->IdGenerators[$modelClass] = $idGenerator ?? $this->idGenerator;
        }
    }

    /**
     * @param $modelObjectOrClassOrName
     * @return RepoInterface
     * @throws UnknownModelException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getModelRepository($modelObjectOrClassOrName): RepoInterface
    {
        $repo = $this->repositoryManager->getRepository($modelObjectOrClassOrName);
        $this->repositories[get_class($repo)] = $repo;
        return $repo;
    }

    public function getLocker(): LockerInterface
    {
        return $this->locker;
    }

    /**
     * @param Link $link
     * @return ModelInterface|null
     * @throws UnknownModelException
     */
    public function findByLink(Link $link): ?ModelInterface
    {
        $repo = $this->getModelRepository($link->getModelName());
        return $repo->findById($link->getId());
    }

    /**
     * @param Link[] $links
     * @return SplObjectStorage
     * @throws InvalidArgumentException
     * @throws UnknownModelException
     */
    public function findByLinks(array $links): SplObjectStorage
    {
        $groups = [];
        foreach ($links as $link) {
            if (!($link instanceof Link)) {
                throw new InvalidArgumentException("Every link should be instance of " . Link::class);
            }
            $name = $link->getModelName();
            $id = (string) $link->getId();
            $groups[$name][$id] = $link;
        }

        $result = new SplObjectStorage();
        foreach ($groups as $modelName => $indexedLinks) {
            $repo = $this->getModelRepository($modelName);
            $models = $repo->findByIds(array_keys($indexedLinks));
            foreach ($models as $model) {
                foreach ($indexedLinks as $link) {
                    if ($link->isFor($model)) {
                        $result[$link] = $model;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param ModelInterface|Link|Id|string|int $argument
     * @param string|null $modelNameOrClass
     * @return ModelInterface|null
     * @throws UnknownModelException
     */
    public function findByAnyTypeId($argument, string $modelNameOrClass = null): ?ModelInterface
    {
        if ($argument instanceof ModelInterface) {
            return $argument;
        }

        if ($argument instanceof Link) {
            return $this->findByLink($argument);
        }

        $repo = $this->getModelRepository($modelNameOrClass);
        return $repo->findById($argument);
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
     * @param ModelInterface|null $locker
     * @param int|null $lockTimeout
     * @return Commit
     * @throws NotModelInterfaceException
     * @throws Throwable
     */
    public function commit(ModelInterface $locker = null, int $lockTimeout = null): Commit
    {
        $commit = new Commit($this->persisted, $this->deleted);

        //Assigns real ids
        foreach ($commit->getPersisted() as $model) {
            ($this->idGenerator)($model);
        }

        ($this->onBeforeCommit)($this, $commit);
        $models = array_merge($commit->getPersisted(), $commit->getDeleted());

        try {
            $this->lock($models, $locker, $lockTimeout);

            $modelClasses = array_unique(array_map('get_class',$models));
            foreach ($modelClasses as $modelClass) {
                $this->getModelRepository($modelClass)->commit($commit);
            }

            ($this->onAfterCommit)($this, $commit);

            $this->unlock($models, $locker);

        } catch (Throwable $exception) {
            $this->unlock($models, $locker);
            ($this->onCommitException)($this, $commit);
            throw $exception;
        }

        return $commit;
    }

    /**
     * Освобождает из памяти всех репозиториев загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     *
     */
    public function freeUpMemory()
    {
        foreach ($this->repositories as $repository) {
            $repository->freeUpMemory();
        }
    }

    /**
     * @param ModelInterface[] $models
     * @param ModelInterface|null $locker
     * @param int $timeout
     * @throws LockedModelException
     */
    protected function lock(array $models, ?ModelInterface $locker, int $timeout)
    {
        if ($locker) {
            foreach ($models as $model) {
                if ($this->getLocker()->isLockedFor($model, $locker)) {
                    throw new LockedModelException();
                }

                if ($locker) {
                    $this->getLocker()->lock($model, $locker, $timeout);
                }
            }
        }
    }

    /**
     * @param ModelInterface[] $models
     * @param ModelInterface|null $locker
     */
    protected function unlock(array $models, ?ModelInterface $locker)
    {
        if ($locker) {
            foreach ($models as $model) {
                $this->getLocker()->unlock($model, $locker);
            }
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

}