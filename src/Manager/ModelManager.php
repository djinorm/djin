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
use DjinORM\Djin\Locker\Lock\ServiceLock;
use DjinORM\Djin\Locker\Lock\Lock;
use DjinORM\Djin\Locker\LockerInterface;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Link;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Repository\Repository;
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
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var Repository[]
     */
    private $repositories;
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
        ConfigManager $configManager,
        LockerInterface $locker,
        callable $onBeforeCommit = null,
        callable $onAfterCommit = null,
        callable $onCommitException = null
    )
    {
        $this->configManager = $configManager;
        $this->locker = $locker;

        $this->onBeforeCommit = $onBeforeCommit ?? function () {};
        $this->onAfterCommit = $onAfterCommit ?? function () {};
        $this->onCommitException = $onCommitException ?? function () {};
    }

    public function getLocker(): LockerInterface
    {
        return $this->locker;
    }

    /**
     * @param $modelObjectOrClassOrName
     * @return Repository
     * @throws InvalidArgumentException
     * @throws UnknownModelException
     */
    public function getRepository($modelObjectOrClassOrName): Repository
    {
        $repo = $this->configManager->getRepository($modelObjectOrClassOrName);
        $this->repositories[get_class($repo)] = $repo;
        return $repo;
    }

    /**
     * @param Link $link
     * @return ModelInterface|null
     * @throws InvalidArgumentException
     * @throws UnknownModelException
     */
    public function findByLink(Link $link): ?ModelInterface
    {
        $repo = $this->getRepository($link->getModelName());
        return $repo->findById($link->id());
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
                throw new InvalidArgumentException("Every link should be instance of " . Link::class, 1);
            }
            $name = $link->getModelName();
            $id = (string) $link->id();
            $groups[$name][$id] = $link;
        }

        $result = new SplObjectStorage();
        foreach ($groups as $modelName => $indexedLinks) {
            $repo = $this->getRepository($modelName);
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
     * @throws InvalidArgumentException
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

        $repo = $this->getRepository($modelNameOrClass);
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

    public function isPersisted(ModelInterface $model): bool
    {
        $hash = spl_object_hash($model);
        return isset($this->persisted[$hash]);
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

    public function isDeleted(ModelInterface $model): bool
    {
        $hash = spl_object_hash($model);
        return isset($this->deleted[$hash]);
    }

    public function resetDeleted(): void
    {
        $this->deleted = [];
    }

    /**
     * @param Lock $lock
     * @return Commit
     * @throws InvalidArgumentException
     * @throws NotModelInterfaceException
     * @throws Throwable
     * @throws UnknownModelException
     */
    public function commit(Lock $lock): Commit
    {
        $commit = new Commit($this->persisted, $this->deleted);

        //Assigns real ids
        foreach ($commit->getPersisted() as $model) {
            $idGenerator = $this->configManager->getIdGenerator($model);
            $idGenerator($model);
        }

        ($this->onBeforeCommit)($commit);
        $models = array_merge($commit->getPersisted(), $commit->getDeleted());

        try {
            $this->lock($models, $lock);

            $modelClasses = array_unique(array_map('get_class',$models));
            foreach ($modelClasses as $modelClass) {
                $this->getRepository($modelClass)->commit($commit);
            }

            ($this->onAfterCommit)($commit);

            if ($lock instanceof ServiceLock) {
                $this->unlock($models, $lock);
            }

        } catch (Throwable $exception) {

            if ($lock instanceof ServiceLock) {
                $this->unlock($models, $lock);
            }

            ($this->onCommitException)($commit);
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
     * @param Lock $lock
     * @throws LockedModelException
     */
    protected function lock(array $models, ?Lock $lock)
    {
        if ($lock) {
            foreach ($models as $model) {
                if ($this->getLocker()->isLockedFor($model, $lock->getLocker())) {
                    throw new LockedModelException();
                }

                if ($lock) {
                    $this->getLocker()->lock($model, $lock->getLocker(), $lock->getTimeout());
                }
            }
        }
    }

    /**
     * @param ModelInterface[] $models
     * @param Lock|null $lock
     */
    protected function unlock(array $models, ?Lock $lock)
    {
        if ($lock) {
            foreach ($models as $model) {
                $this->getLocker()->unlock($model, $lock->getLocker());
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