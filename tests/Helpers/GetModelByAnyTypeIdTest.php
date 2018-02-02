<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 07.07.2017 2:33
 */

namespace DjinORM\Djin\Helpers;

use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Exceptions\NotFoundException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;

class GetModelByAnyTypeIdTest extends TestCase
{

    /** @var ModelInterface */
    private $model;

    /** @var RepositoryInterface */
    private $repo;

    public function setUp()
    {
        $this->model = $this->createMock(ModelInterface::class);
        $this->model->method('getId')->willReturn(new Id(1));

        $this->repo = $this->createMock(RepositoryInterface::class);
        $this->repo->method('findById')->willReturnCallback(function ($id) {
            if ($id == 1) {
                return $this->model;
            }
            return null;
        });
    }

    public function testGetByModel()
    {
        $model = GetModelByAnyTypeIdHelper::get($this->model, $this->repo);
        $this->assertSame($this->model, $model);
    }

    public function testGetByScalarId()
    {
        $model = GetModelByAnyTypeIdHelper::get(1, $this->repo);
        $this->assertSame($this->model, $model);
    }

    public function testGetByIdObject()
    {
        $model = GetModelByAnyTypeIdHelper::get($this->model->getId(), $this->repo);
        $this->assertSame($this->model, $model);
    }

    public function testGetByModelWithoutRepo()
    {
        $model = GetModelByAnyTypeIdHelper::get($this->model);
        $this->assertSame($this->model, $model);
    }

    public function testGetByScalarIdWithoutRepo()
    {
        $this->expectException(LogicException::class);
        GetModelByAnyTypeIdHelper::get(1);
    }

    public function testGetByIdObjectWithoutRepo()
    {
        $this->expectException(LogicException::class);
        GetModelByAnyTypeIdHelper::get($this->model->getId());
    }

    public function testGetByIncorrectIdType()
    {
        $this->expectException(InvalidArgumentException::class);
        GetModelByAnyTypeIdHelper::get(null, $this->repo);
    }

    public function testNotFoundByScalarId()
    {
        $this->expectException(NotFoundException::class);
        GetModelByAnyTypeIdHelper::get(10, $this->repo);
    }

    public function testNotFoundByIdObject()
    {
        $this->expectException(NotFoundException::class);
        GetModelByAnyTypeIdHelper::get(new Id(777), $this->repo);
    }

}
