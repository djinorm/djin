<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 03.04.2017 23:41
 */

namespace DjinORM\Djin;


use DjinORM\Djin\Manager\ModelManager;
use DjinORM\Djin\Id\MemoryIdGenerator;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Mock\TestModelRepository;
use PHPUnit\Framework\TestCase;

abstract class DjinTestCase extends TestCase
{

    /** @var ModelManager */
    public $manager;

    /** @var TestModelRepository */
    public $repository;

    public function setUp()
    {
        $this->manager = new ModelManager(new MemoryIdGenerator());
        $this->manager->setModelConfig(TestModel::class, new TestModelRepository(), new MemoryIdGenerator(8));
        $this->repository = $this->manager->getModelConfig(TestModel::class)->getRepository();
    }

}