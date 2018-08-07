<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:20
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Model;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class ModelPointerTest extends TestCase
{

    /** @var ModelPointer */
    private $pointer;

    /** @var ModelInterface */
    private $model;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new TestModel(1);
        $this->pointer = new ModelPointer($this->model);
    }

    public function testConstructFromNameAndId()
    {
        $id = new Id(1);
        $pointer = new ModelPointer(TestModel::getModelName(), $id);
        $this->assertSame($id, $pointer->getId());
    }

    public function testConstructFromNameAndScalarId()
    {
        $pointer = new ModelPointer(TestModel::getModelName(), 1);
        $this->assertInstanceOf(Id::class, $pointer->getId());
        $this->assertEquals(1, $pointer->getId()->toScalar());
    }

    public function testGetId()
    {
        $this->assertSame($this->model->getId(), $this->pointer->getId());
    }

    public function testGetModelName()
    {
        $this->assertEquals($this->model::getModelName(), $this->pointer->getModelName());
    }

}
