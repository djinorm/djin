<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 21.06.2017 22:33
 */

namespace DjinORM\Djin\Manager;


use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Repository\RepositoryInterface;
use DjinORM\Djin\Id\MemoryIdGenerator;
use DjinORM\Djin\Mock\TestModelRepository;
use PHPUnit\Framework\TestCase;

class ModelConfigTest extends TestCase
{

    /** @var  RepositoryInterface */
    private $repository;

    /** @var  IdGeneratorInterface */
    private $idGenerator;

    /** @var  ModelConfig */
    private $config;

    public function setUp()
    {
        $this->repository = new TestModelRepository();
        $this->idGenerator = new MemoryIdGenerator();
        $this->config = new ModelConfig($this->repository, $this->idGenerator);
    }

    public function testGetRepository()
    {
        $config = $this->config;
        $this->assertEquals($this->repository, $config->getRepository());
    }

    public function testGetIdGenerator()
    {
        $config = $this->config;
        $this->assertEquals($this->idGenerator, $config->getIdGenerator());
    }

}