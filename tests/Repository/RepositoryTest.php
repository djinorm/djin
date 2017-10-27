<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 05.04.2017 8:22
 */

namespace DjinORM\Djin\Repository;


use DjinORM\Djin\Exceptions\NotFoundException;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Mock\TestModelRepository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{

    /** @var TestModelRepository */
    private $repo;

    public function setUp()
    {
        $this->repo = new TestModelRepository();
    }

    public function testQueryCount()
    {
        $this->assertEquals(0, $this->repo->getQueryCount());
        $this->repo->findById(1);
        $this->assertEquals(1, $this->repo->getQueryCount());
    }

    public function testFindById()
    {
        /** @var TestModel $model */
        $model = $this->repo->findById(1);
        $this->assertEquals(1, $model->getId()->toScalar());
        $this->assertFalse($model->getOtherId()->isPermanent());
    }

    public function testLoadedById()
    {
        $this->repo->findById(1);
        $this->repo->findById(1);
        $this->repo->findById(2);
        $this->repo->findById(2);
        self::assertEquals(2, $this->repo->getQueryCount());
    }

    public function testNotFoundLoadedById()
    {
        $this->repo->findById(7);
        $this->repo->findById(7);
        $this->repo->findById(7);
        $this->repo->findById(7);
        self::assertEquals(4, $this->repo->getQueryCount());
    }

    public function testFindByIds()
    {
        /** @var TestModel[] $models */
        $models = $this->repo->findByIds([1,2]);
        $this->assertEquals(1, $models[1]->getId()->toScalar());
        $this->assertEquals(2, $models[2]->getId()->toScalar());
    }

    public function testLoadedByIds()
    {
        $this->repo->findByIds([1,2]);
        $this->repo->findByIds([2,1]);
        self::assertEquals(1, $this->repo->getQueryCount());
    }

    public function testFindByExistedIdOrException()
    {
        /** @var TestModel $model */
        $model = $this->repo->findByIdOrException(1);
        $this->assertEquals(1, $model->getId()->toScalar());
        $this->assertFalse($model->getOtherId()->isPermanent());
    }

    public function testFindByNonExistedIdOrException()
    {
        $this->expectException(NotFoundException::class);
        $this->repo->findByIdOrException(777);
    }

    public function testFindByNonExistedIdOrExceptionWithCustomExceptionClass()
    {
        $exception = new \LogicException();
        $this->expectException(\LogicException::class);
        $this->repo->findByIdOrException(777, $exception);
    }

    public function testFreeUpMemory()
    {
        $this->repo->findById(1);
        $this->repo->findById(1);
        $this->assertEquals(1, $this->repo->getQueryCount());
        $this->repo->freeUpMemory();
        $this->repo->findById(1);
    }

}