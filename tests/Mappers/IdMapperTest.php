<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 28.10.2019 0:59
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\SerializerException;
use DjinORM\Djin\Id\Id;
use Throwable;

class IdMapperTest extends MapperTestCase
{

    public function serializeDataProvider(): array
    {
        return [
            [new Id(1), '1'],
            [new Id('1'), '1'],
            [new Id('string'), 'string'],
        ];
    }

    public function serializeInvalidDataProvider(): array
    {
        return [
            [1],
            ['string'],
            [null],
            [false],
            [[]],
            [new Id()],
        ];
    }

    public function deserializeDataProvider(): array
    {
        return [
            ['1', new Id(1)],
            ['1', new Id('1')],
            ['string', new Id('string')],
        ];
    }

    public function deserializeInvalidDataProvider(): array
    {
        return [
            [null],
            [[]],
            [false],
        ];
    }

    /**
     * @param $input
     * @throws SerializerException
     * @dataProvider deserializeInvalidDataProvider
     */
    public function testInvalidSerialize($input)
    {
        $this->expectException(Throwable::class);
        $this->getMapper()->serialize($input);
    }

    /**
     * @param $input
     * @throws SerializerException
     * @dataProvider deserializeInvalidDataProvider
     */
    public function testInvalidDeserialize($input)
    {
        $this->expectException(Throwable::class);
        $this->getMapper()->deserialize($input);
    }

    protected function getMapper(): MapperInterface
    {
        return new IdMapper();
    }
}
