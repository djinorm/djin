<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 11:39
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\TestHelpers;

use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class IdComparatorTest extends TestCase
{

    public function testCompareTrueWithComparator()
    {
        $model_1 = new TestModel(1);
        $model_1->getOtherId()->setPermanentId(1);

        $model_2 = new TestModel(1);
        $model_2->getOtherId()->setPermanentId(1);

        $this->assertEquals($model_1, $model_2);
    }

    public function testCompareFalseWithComparator()
    {
        $model_1 = new TestModel(1);
        $model_1->getOtherId()->setPermanentId(1);

        $model_2 = new TestModel(1);
        $model_2->getOtherId()->setPermanentId(2);

        $this->assertNotEquals($model_1, $model_2);
    }

}
