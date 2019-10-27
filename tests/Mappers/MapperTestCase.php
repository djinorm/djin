<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 28.10.2019 0:07
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;
use PHPUnit\Framework\TestCase;

abstract class MapperTestCase extends TestCase
{

    abstract public function serializeDataProvider(): array;

    abstract public function serializeInvalidDataProvider(): array;

    abstract public function deserializeDataProvider(): array;

    abstract public function deserializeInvalidDataProvider(): array;

    /**
     * @param $input
     * @param $output
     * @throws SerializerException
     * @dataProvider serializeDataProvider
     */
    public function testSerialize($input, $output)
    {
        $this->assert(
            $output,
            $this->getMapper()->serialize($input)
        );
    }

    /**
     * @param $input
     * @throws SerializerException
     * @dataProvider serializeInvalidDataProvider
     */
    public function testInvalidSerialize($input)
    {
        $this->expectException(SerializerException::class);
        $this->getMapper()->serialize($input);
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider deserializeDataProvider
     */
    public function testDeserialize($input, $output)
    {
        $this->assert(
            $output,
            $this->getMapper()->deserialize($input)
        );
    }

    /**
     * @param $input
     * @throws SerializerException
     * @dataProvider deserializeInvalidDataProvider
     */
    public function testInvalidDeserialize($input)
    {
        $this->expectException(SerializerException::class);
        $this->getMapper()->deserialize($input);
    }

    protected function assert($expected, $actual)
    {
        if (is_object($actual)) {
            $this->assertEquals(
                $expected,
                $actual
            );
        } else {
            $this->assertSame(
                $expected,
                $actual
            );
        }
    }

    abstract protected function getMapper(): MapperInterface;

}