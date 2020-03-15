<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 20:02
 */

namespace DjinORM\Djin\Model;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel_1;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{

    public function testEmptyModelConstruct()
    {
        $model = new TestModel_1();
        self::assertInstanceOf(Id::class, $model->id());
        self::assertInstanceOf(Id::class, $model->getOtherId());
        self::assertFalse($model->id()->isPermanent());
        self::assertFalse($model->getOtherId()->isPermanent());
        return $model;
    }

    public function testFilledModelConstruct()
    {
        $model = new TestModel_1(2,1);
        self::assertInstanceOf(Id::class, $model->id());
        self::assertInstanceOf(Id::class, $model->getOtherId());
        $this->assertEquals(2, (string) $model->id());
        self::assertTrue($model->id()->isPermanent());
        self::assertTrue($model->getOtherId()->isPermanent());
        return $model;
    }

    /**
     * @depends testEmptyModelConstruct
     * @param TestModel_1 $model
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function testSetOther(TestModel_1 $model)
    {
        $other = new TestModel_1();
        $model->setOtherModel($other);

        self::assertFalse($model->getOtherId()->isPermanent());
        self::assertEquals($other->id(), $model->getOtherId());

        $other->id()->assign(7);
        self::assertTrue($model->getOtherId()->isPermanent());
    }

}