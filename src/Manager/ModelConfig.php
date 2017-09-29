<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 19.03.2017 17:38
 */

namespace DjinORM\Djin\Manager;


use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Repository\RepositoryInterface;

class ModelConfig
{

    private $idGenerator;

    private $repository;

    public function __construct(RepositoryInterface $repository, IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
        $this->repository = $repository;
    }

    public function getRepository():RepositoryInterface
    {
        return $this->repository;
    }

    public function getIdGenerator():IdGeneratorInterface
    {
        return $this->idGenerator;
    }

}