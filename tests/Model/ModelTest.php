<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 20:02
 */

namespace DjinORM\Djin\Model;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{

    public function testEmptyModelConstruct()
    {
        $model = new TestModel();
        self::assertInstanceOf(Id::class, $model->getId());
        self::assertInstanceOf(Id::class, $model->getOtherId());
        self::assertFalse($model->getId()->isPermanent());
        self::assertFalse($model->getOtherId()->isPermanent());
        return $model;
    }

    public function testFilledModelConstruct()
    {
        $model = new TestModel(2,1);
        self::assertInstanceOf(Id::class, $model->getId());
        self::assertInstanceOf(Id::class, $model->getOtherId());
        $this->assertEquals(2, $model->getId()->toScalar());
        self::assertTrue($model->getId()->isPermanent());
        self::assertTrue($model->getOtherId()->isPermanent());
        return $model;
    }

    /**
     * @depends testEmptyModelConstruct
     * @param $model
     */
    public function testSetOther(TestModel $model)
    {
        $other = new TestModel();
        $model->setOtherModel($other);

        self::assertFalse($model->getOtherId()->isPermanent());
        self::assertEquals($other->getId(), $model->getOtherId());

        $other->getId()->setPermanentId(7);
        self::assertTrue($model->getOtherId()->isPermanent());
    }

}