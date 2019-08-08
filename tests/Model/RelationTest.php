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

class RelationTest extends TestCase
{

    /** @var Relation */
    private $relation;

    /** @var ModelInterface */
    private $model;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new TestModel(1);
        $this->relation = Relation::link($this->model);
    }

    public function testConstructFromNameAndId()
    {
        $id = new Id(1);
        $pointer = new Relation(TestModel::getModelName(), $id);
        $this->assertSame($id, $pointer->getId());
    }

    public function testConstructFromNameAndScalarId()
    {
        $relation = new Relation(TestModel::getModelName(), new Id(1));
        $this->assertInstanceOf(Id::class, $relation->getId());
        $this->assertEquals(1, $relation->getId()->toScalar());
    }

    public function testGetId()
    {
        $this->assertSame($this->model->getId(), $this->relation->getId());
    }

    public function testGetModelName()
    {
        $this->assertEquals($this->model::getModelName(), $this->relation->getModelName());
    }

    public function testToJson()
    {
        $expected = json_encode([
            'model' => $this->model::getModelName(),
            'id' => $this->model->getId()->toScalar(),
        ]);
        $this->assertEquals($expected, json_encode($this->relation));
    }

    public function testFromJson()
    {
        $json = json_encode($this->relation);
        $relation = Relation::fromJson($json);
        $this->assertEquals($this->relation, $relation);
    }

    public function testFromJsonNullOrFail()
    {
        $this->assertNull(Relation::fromJson(''));
        $this->assertNull(Relation::fromJson('null'));
        $this->assertNull(Relation::fromJson('[]'));
        $this->assertNull(Relation::fromJson('{}'));
        $this->assertNull(Relation::fromJson('{"model": "name"}'));
        $this->assertNull(Relation::fromJson('{"id": 123}'));
    }

}
