<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 19:46
 */

namespace DjinORM\Djin\tests\Unit\Id;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Id\Id;
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

    public function testConstructWrongPermanentType()
    {
        $this->expectException(InvalidArgumentException::class);
        new Id([]);
    }

    public function testGetPermanentOrNull()
    {
        $this->assertEquals(1, $this->permanent->getPermanentOrNull());
        $this->assertNull($this->temp->getPermanentOrNull());
    }

    public function testGetTempId()
    {
        $this->assertContains('__DJIN__', $this->temp->getTempId());
    }

    public function testSetPermanentIdInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->temp->setPermanentId([]);
    }

    public function testSetAlreadySettedPermanentId()
    {
        $this->expectException(LogicException::class);
        $this->permanent->setPermanentId(1);
    }

    public function testSetPermanentId()
    {
        $this->temp->setPermanentId(1);
        $this->assertTrue($this->temp->isPermanent());
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
    
    public function testToScalar()
    {
        $this->assertEquals(1, $this->permanent->toScalar());
        $this->assertNotEquals($this->permanent->getTempId(), $this->permanent->toScalar());
        $this->assertEquals($this->temp->getTempId(), $this->temp->toScalar());
    }

    public function testToString()
    {
        $this->assertEquals(1, (string) $this->permanent);
        $this->assertNotEquals($this->permanent->getTempId(), (string) $this->permanent);
        $this->assertEquals($this->temp->getTempId(), (string) $this->temp);
    }

}