<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 30.09.2017 0:10
 */

namespace DjinORM\Djin\Helpers;


use ArrayObject;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel_1;
use PHPUnit\Framework\TestCase;

class ReflectionHelperTest extends TestCase
{

    /** @var TestModel_1 */
    private $model;

    /** @var ArrayObject */
    private $arrayAccess;

    protected function setUp(): void
    {
        $this->model = new TestModel_1(1, 2);
        $this->arrayAccess = new ArrayObject(['key' => 'value']);
    }

    public function testNewWithoutConstructor()
    {
        /** @var TestModel_1 $model */
        $model = ReflectionHelper::newWithoutConstructor(TestModel_1::class);
        $this->assertInstanceOf(TestModel_1::class, $model);
        $this->assertEquals(null, $model->getOtherId());
    }

    public function testGetProperty()
    {
        $this->assertSame(
            $this->model->getOtherId(),
            ReflectionHelper::getProperty($this->model, 'otherId')
        );
    }

    public function testGetPropertyArrayAccess()
    {
        $this->assertSame(
            $this->arrayAccess['key'],
            ReflectionHelper::getProperty($this->arrayAccess, 'key')
        );
    }

    public function testSetProperty()
    {
        $id = new Id(3);
        ReflectionHelper::setProperty($this->model, 'otherId', $id);
        $this->assertSame(
            $id,
            $this->model->getOtherId()
        );
    }

    public function testSetPropertyArrayAccess()
    {
        $id = new Id(3);
        ReflectionHelper::setProperty($this->arrayAccess, 'otherId', $id);
        $this->assertSame(
            $id,
            $this->arrayAccess['otherId']
        );
    }
}
