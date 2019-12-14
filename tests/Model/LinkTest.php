<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:20
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Model;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel_1;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{

    /** @var Link */
    private $link;

    /** @var ModelInterface */
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TestModel_1(1);
        $this->link = Link::to($this->model);
    }

    public function testConstructFromNameAndId()
    {
        $id = new Id(1);
        $pointer = new Link(TestModel_1::getModelName(), $id);
        $this->assertSame($id, $pointer->getId());
    }

    public function testConstructFromNameAndScalarId()
    {
        $relation = new Link(TestModel_1::getModelName(), new Id(1));
        $this->assertInstanceOf(Id::class, $relation->getId());
        $this->assertEquals(1, $relation->getId()->toString());
    }

    public function testGetId()
    {
        $this->assertSame($this->model->getId(), $this->link->getId());
    }

    public function testGetModelName()
    {
        $this->assertEquals($this->model::getModelName(), $this->link->getModelName());
    }

    public function testToJson()
    {
        $expected = json_encode([
            'model' => $this->model::getModelName(),
            'id' => $this->model->getId()->toString(),
        ]);
        $this->assertEquals($expected, json_encode($this->link));
    }

    public function testFromJson()
    {
        $json = json_encode($this->link);
        $link = Link::fromJson($json);
        $this->assertEquals($this->link, $link);
    }

    public function testFromJsonNullOrFail()
    {
        $this->assertNull(Link::fromJson(''));
        $this->assertNull(Link::fromJson('null'));
        $this->assertNull(Link::fromJson('[]'));
        $this->assertNull(Link::fromJson('{}'));
        $this->assertNull(Link::fromJson('{"model": "name"}'));
        $this->assertNull(Link::fromJson('{"id": 123}'));
    }

}
