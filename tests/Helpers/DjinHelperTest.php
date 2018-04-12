<?php
/**
 * Created for DjinORM.
 * Datetime: 28.11.2017 17:00
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Helpers;

use DjinORM\Djin\Exceptions\MismatchModelException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Mock\TestSecondModel;
use DjinORM\Djin\Model\ModelInterface;
use PHPUnit\Framework\TestCase;

class DjinHelperTest extends TestCase
{

    public function testGetScalarIds()
    {
        $array = [
            new TestModel(10),
            new TestModel(20),
            new TestModel(30),
            new Id(4),
            new Id(5),
        ];

        $scalarIds = DjinHelper::getScalarIds($array, TestModel::class);
        $this->assertEquals([10, 20, 30, 4, 5], $scalarIds);
    }


    public function testGetScalarIdsWithInvalidCheckModelClass()
    {
        $this->expectException(MismatchModelException::class);
        $array = [
            new TestModel(10),
            new TestModel(20),
            new TestSecondModel(30),
            new Id(4),
            new Id(5),
        ];
        DjinHelper::getScalarIds($array, TestModel::class);
    }

    public function testIndexModelsArrayById()
    {
        $model_1 = new TestModel(10);
        $model_2 = new TestModel(20);
        $model_3 = new TestModel(30);

        $models = [
            $model_1,
            $model_2,
            $model_3,
        ];

        $this->assertEquals([10 => $model_1, 20 => $model_2, 30 => $model_3], DjinHelper::indexModelsArrayById($models));
    }

    public function testIndexModelsArrayByCallback()
    {
        $model_1 = new TestModel(10);
        $model_2 = new TestModel(20);
        $model_3 = new TestModel(30);

        $models = [
            $model_1,
            $model_2,
            $model_3,
        ];

        $this->assertEquals([10 => $model_1, 20 => $model_2, 30 => $model_3], DjinHelper::indexModelsArrayCallback($models, function (ModelInterface $model) {
            return $model->getId()->toScalar();
        }));
    }

    public function testGetScalarIdOrNull()
    {
        $id = null;
        $this->assertNull(DjinHelper::getScalarIdOrNull($id));

        $id = new Id(10);
        $this->assertEquals(10, DjinHelper::getScalarIdOrNull($id));
    }

}
