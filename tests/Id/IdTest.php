<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 19:46
 */

namespace DjinORM\Djin\Id;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Model\ModelInterface;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{

    /** @var Id */
    private $permanent;

    /** @var Id */
    private $temp;

    public function setUp()
    {
        $this->permanent = new Id(1);
        $this->temp = new Id();
    }

    public function testConstructWithoutPermanent()
    {
        $this->assertFalse($this->temp->isPermanent());
    }

    public function testConstructWithPermanent()
    {
        $this->assertTrue($this->permanent->isPermanent());
    }

    public function testGetPermanentOrNull()
    {
        $this->assertEquals(1, $this->permanent->getPermanentOrNull());
        $this->assertNull($this->temp->getPermanentOrNull());
    }

    public function testSetAlreadyAssignedPermanentId()
    {
        $this->assertFalse($this->permanent->assign(1));
    }

    public function testSetPermanentId()
    {
        $this->assertTrue($this->temp->assign(1));
    }

    public function testIsEqual()
    {
        $modelPermanent = $this->createMock(ModelInterface::class);
        $modelPermanent->method('getId')->willReturn($this->permanent);

        $modelTemp = $this->createMock(ModelInterface::class);
        $modelTemp->method('getId')->willReturn($this->temp);

        $id = new Id(1);

        $this->assertTrue($this->permanent->isEqual($id));
        $this->assertTrue($this->permanent->isEqual(1));
        $this->assertTrue($this->permanent->isEqual($modelPermanent));
        $this->assertFalse($this->permanent->isEqual($this->temp));
        $this->assertFalse($this->permanent->isEqual(2));
        $this->assertFalse($this->permanent->isEqual($modelTemp));

        /** @noinspection PhpParamsInspection */
        $this->assertFalse($this->permanent->isEqual([]));
    }
    
    public function testToString()
    {
        $this->assertEquals(1, $this->permanent->toString());
        $this->assertNull($this->temp->toString());
    }

    public function testAsString()
    {
        $this->assertEquals(1, (string) $this->permanent);
        $this->assertEquals($this->temp->toString(), '');
    }

    public function testNonStrictCompare()
    {
        $id1 = new Id(10);
        $id2 = new Id();
        $id2->assign(10);
        $this->assertTrue($id1 == $id2);

        $id1 = new Id();
        $id2 = new Id();
        $this->assertTrue($id1 == $id2);

        $id1 = new Id(1);
        $id2 = new Id();
        $this->assertFalse($id1 == $id2);

        $id1 = new Id();
        $id2 = new Id();
        $id2->assign(2);
        $this->assertFalse($id1 == $id2);

        $id1 = new Id();
        $id2 = null;
        $this->assertFalse($id1 == $id2);
    }

}