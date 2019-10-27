<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.10.2019 22:39
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\SerializerException;
use PHPUnit\Framework\TestCase;

class StringMapperTest extends TestCase
{

    /** @var StringMapper */
    private $mapper;

    public function dataProvider(): array
    {
        return [
            'string' => ['string', 'string'],
            '0' => [0, '0'],
            '1' => [1, '1'],
            '1.1' => [1.1, '1.1'],
            'true' => [true, '1'],
            'false' => [false, ''],
        ];
    }

    public function invalidDataProvider(): array
    {
        return [
            [null],
            [new StringMapper()],
            [[]],
        ];
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider dataProvider
     */
    public function testSerialize($input, $output)
    {
        $this->assertSame(
            $output,
            $this->mapper->serialize($input)
        );
    }

    /**
     * @param $input
     * @throws SerializerException
     * @dataProvider invalidDataProvider
     */
    public function testInvalidSerialize($input)
    {
        $this->expectException(SerializerException::class);
        $this->mapper->serialize($input);
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider dataProvider
     */
    public function testDeserialize($input, $output)
    {
        $this->assertSame(
            $output,
            $this->mapper->deserialize($input)
        );
    }

    /**
     * @param $input
     * @throws SerializerException
     * @dataProvider invalidDataProvider
     */
    public function testInvalidDeserialize($input)
    {
        $this->expectException(SerializerException::class);
        $this->mapper->deserialize($input);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mapper = new StringMapper();
    }
}
