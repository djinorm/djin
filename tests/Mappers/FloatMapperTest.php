<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 25.10.2019 17:29
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\SerializerException;
use PHPUnit\Framework\TestCase;

class FloatMapperTest extends TestCase
{

    private $mapper;

    public function floatDataProvider(): array
    {
        return [
            [0],
            [1],
            [-1],
            [100],
            [0.1],
            [1.1],
            [-1.1],
            [100.001],
        ];
    }

    public function stringDataProvider(): array
    {
        return [
            ['0'],
            ['1'],
            ['-1'],
            ['100'],
            ['0'],
            ['0.1'],
            ['1.1'],
            ['-1.1'],
            ['100.001'],
        ];
    }

    public function invalidDataProvider(): array
    {
        return [
            ['000'],
            ['-0'],
            ['qwerty'],
            ['+1'],
            ['000'],
            ['-0'],
            ['qwerty'],
            ['-0.0'],
            ['002.00'],
            ['-002.00'],
            ['2.'],
            ['.2'],
        ];
    }

    /**
     * @param $data
     * @dataProvider floatDataProvider
     */
    public function testSerialize($data)
    {
        $this->assertEquals($this->mapper->serialize($data), $data);
    }

    /**
     * @param $data
     * @throws SerializerException
     * @dataProvider invalidDataProvider
     */
    public function testInvalidSerializeData($data)
    {
        $this->expectException(SerializerException::class);
        $this->mapper->serialize($data);
    }

    /**
     * @param $data
     * @dataProvider floatDataProvider
     * @dataProvider stringDataProvider
     */
    public function testDeserialize($data)
    {
        $this->assertEquals($this->mapper->deserialize($data), (float) $data);
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
        $this->mapper = new FloatMapper();
    }
}
