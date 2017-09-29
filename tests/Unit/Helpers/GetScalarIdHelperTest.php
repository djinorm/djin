<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 20:15
 */

namespace DjinORM\Djin\tests\Unit\Helpers;

use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\MismatchModelException;
use DjinORM\Djin\Exceptions\NotPermanentIdException;
use DjinORM\Djin\Helpers\GetScalarIdHelper;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\tests\Mock\TestModel;
use DjinORM\Djin\tests\Mock\TestSecondModel;
use PHPUnit\Framework\TestCase;

class GetScalarIdHelperTest extends TestCase
{

    public function testGetIdFromModelWithClassCheck()
    {
        $model = new TestModel(1);
        $this->assertEquals(1, GetScalarIdHelper::get($model, TestModel::class));
    }

    public function testGetIdFromModelWithWrongClassCheck()
    {
        $model = new TestModel(1);
        $this->expectException(MismatchModelException::class);
        $this->assertEquals(1, GetScalarIdHelper::get($model, TestSecondModel::class));
    }

    public function testGetIdFromModelNotPermanent()
    {
        $model = new TestModel();
        $this->expectException(NotPermanentIdException::class);
        GetScalarIdHelper::get($model);
    }

    public function testGetIdFromId()
    {
        $id = new Id(1);
        $this->assertEquals(1, GetScalarIdHelper::get($id));
    }

    public function testGetIdFromIdNotPermanent()
    {
        $id = new Id();
        $this->expectException(NotPermanentIdException::class);
        GetScalarIdHelper::get($id);
    }

    public function testGetIdFromScalar()
    {
        $this->assertEquals(1, GetScalarIdHelper::get(1));
        $this->assertEquals('1', GetScalarIdHelper::get('1'));
    }

    public function testGetIdFromNull()
    {
        $this->expectException(InvalidArgumentException::class);
        GetScalarIdHelper::get(null);
    }

    public function testGetIdFromWrongClass()
    {
        $this->expectException(InvalidArgumentException::class);
        GetScalarIdHelper::get($this);
    }

}
