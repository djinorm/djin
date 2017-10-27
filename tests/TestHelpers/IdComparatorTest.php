<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27.10.2017
 * Time: 11:57
 */


namespace DjinORM\Djin\TestHelpers;

use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class IdComparatorTest extends TestCase
{

    public function testCompareWithoutComparator()
    {
        $model_1 = new TestModel();
        $model_1->getOtherId()->getTempId();
        $model_1->getOtherId()->setPermanentId(1);

        $model_2 = new TestModel();
        $model_2->getOtherId()->getTempId();
        $model_2->getOtherId()->setPermanentId(1);

        $this->assertNotEquals($model_1, $model_2);
    }

    public function testCompareTrueWithComparator()
    {
        $this->registerComparator(new IdComparator());

        $model_1 = new TestModel();
        $model_1->getOtherId()->getTempId();
        $model_1->getOtherId()->setPermanentId(1);

        $model_2 = new TestModel();
        $model_2->getOtherId()->getTempId();
        $model_2->getOtherId()->setPermanentId(1);

        $this->assertEquals($model_1, $model_2);
    }

    public function testCompareFalseWithComparator()
    {
        $this->registerComparator(new IdComparator());

        $model_1 = new TestModel();
        $model_1->getOtherId()->getTempId();
        $model_1->getOtherId()->setPermanentId(1);

        $model_2 = new TestModel();
        $model_2->getOtherId()->getTempId();
        $model_2->getOtherId()->setPermanentId(2);

        $this->assertNotEquals($model_1, $model_2);
    }

}
