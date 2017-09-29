<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 30.09.2017 0:10
 */

namespace DjinORM\Djin\tests\Unit\Helpers;

use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\tests\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class RepoHelperTest extends TestCase
{

    /** @var TestModel */
    private $model;

    public function setUp()
    {
        $this->model = new TestModel(1, 2);
    }

    public function testNewWithoutConstructor()
    {
        /** @var TestModel $model */
        $model = RepoHelper::newWithoutConstructor(TestModel::class);
        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(null, $model->getOtherId());
    }

    public function testGetProperty()
    {
        $this->assertSame(
            $this->model->getOtherId(),
            RepoHelper::getProperty($this->model, 'otherId')
        );
    }

    public function testSetProperty()
    {
        $id = new Id(3);
        RepoHelper::setProperty($this->model, 'otherId', $id);
        $this->assertSame(
            $id,
            $this->model->getOtherId()
        );
    }

    public function testSetIdFromScalar()
    {
        RepoHelper::setIdFromScalar($this->model, 'otherId', ['otherId' => 7]);
        $this->assertEquals(7, $this->model->getOtherId()->toScalar());
        $this->assertInstanceOf(Id::class, $this->model->getOtherId());
    }

    public function testSetDateTimeImmutable()
    {
        $date = '2017-09-30 00:30:30';
        RepoHelper::setDateTime($this->model, 'custom', ['custom' => $date]);
        $this->assertEquals($date, $this->model->getCustom()->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->model->getCustom());
    }

    public function testSetDateTime()
    {
        $date = '2017-09-30 00:30:30';
        RepoHelper::setDateTime($this->model, 'custom', ['custom' => $date], false);
        $this->assertEquals($date, $this->model->getCustom()->format('Y-m-d H:i:s'));
        $this->assertNotInstanceOf(\DateTimeImmutable::class, $this->model->getCustom());
        $this->assertInstanceOf(\DateTime::class, $this->model->getCustom());
    }

    public function testSetString()
    {
        RepoHelper::setString($this->model, 'custom', ['custom' => 'hello']);
        $this->assertEquals('hello', $this->model->getCustom());
    }

    public function testSetInt()
    {
        RepoHelper::setInt($this->model, 'custom', ['custom' => 777]);
        $this->assertEquals(777, $this->model->getCustom());
    }

    public function testSetFloat()
    {
        RepoHelper::setFloat($this->model, 'custom', ['custom' => 1.23]);
        $this->assertEquals(1.23, $this->model->getCustom());
    }

    public function testSetBool()
    {
        RepoHelper::setBool($this->model, 'custom', ['custom' => 1]);
        $this->assertEquals(true, $this->model->getCustom());

        RepoHelper::setBool($this->model, 'custom', ['custom' => 0]);
        $this->assertEquals(false, $this->model->getCustom());
    }

}
