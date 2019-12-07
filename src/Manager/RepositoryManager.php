<?php


namespace DjinORM\Djin\Manager;


use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\RepoInterface;

class RepositoryManager
{

    /** @var array */
    private $classToRepo;

    /** @var array */
    private $nameToClass;

    public function add($repoOrCallable, array $modelClasses)
    {
        foreach ($modelClasses as $class) {
            /** @var ModelInterface|string $class */
            $this->classToRepo[$class] = $repoOrCallable;
            $this->nameToClass[$class::getModelName()] = $class;
        }
    }

    /**
     * @param $modelObjectOrClassOrName
     * @return RepoInterface
     * @throws UnknownModelException
     */
    public function getRepository($modelObjectOrClassOrName): RepoInterface
    {
        $identifier = $modelObjectOrClassOrName;
        $class = is_object($identifier) ? get_class($identifier) : $identifier;

        $name = null;
        if (!isset($this->classToRepo[$class])) {
            /** @var ModelInterface $class */
            $name = $class::getModelName();
            $class = $this->nameToClass[$name];
        }

        if (isset($this->classToRepo[$class])) {
            $repo = $this->classToRepo[$class];
            if ($repo instanceof RepoInterface) {
                return $repo;
            }

            $this->classToRepo[$class] = $repo();
            return $this->classToRepo[$class];
        }

        if ($name) {
            throw new UnknownModelException("No repository for model with name '{$name}'", 1);
        }

        throw new UnknownModelException("No repository for model with class '{$class}'", 0);
    }

}