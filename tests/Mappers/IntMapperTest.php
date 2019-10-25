<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 25.10.2019 17:29
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\SerializerException;
use PHPUnit\Framework\TestCase;

class IntMapperTest extends TestCase
{

    private $mapper;

    public function integerDataProvider(): array
    {
        return [
            [0],
            [1],
            [-1],
            [100],
        ];
    }

    public function stringDataProvider(): array
    {
        return [
            ['0'],
            ['1'],
            ['-1'],
            ['100'],
        ];
    }

    public function invalidDataProvider(): array
    {
        return [
            ['000'],
            ['-0'],
            ['qwerty'],
            ['+1'],
        ];
    }

    /**
     * @param $data
     * @dataProvider integerDataProvider
     */
    public function testSerialize($data)
    {
        $this->assertSame($this->mapper->serialize($data), $data);
    }

    /**
     * @param $data
     * @throws SerializerException
     * @dataProvider stringDataProvider
     * @dataProvider invalidDataProvider
     */
    public function testInvalidSerializeData($data)
    {
        $this->expectException(SerializerException::class);
        $this->mapper->serialize($data);
    }

    /**
     * @param $data
     * @dataProvider integerDataProvider
     * @dataProvider stringDataProvider
     */
    public function testDeserialize($data)
    {
        $this->assertSame($this->mapper->deserialize($data), (int) $data);
    }

    /**
     * @param $data
     * @dataProvider invalidDataProvider
     */
    public function testInvalidDeserialize($data)
    {
        $this->expectException(SerializerException::class);
        $this->mapper->deserialize($data);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mapper = new IntMapper();
    }
}
