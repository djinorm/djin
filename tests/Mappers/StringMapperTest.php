<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.10.2019 22:39
 */

namespace DjinORM\Djin\Mappers;


class StringMapperTest extends MapperTestCase
{

    public function serializeDataProvider(): array
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

    public function serializeInvalidDataProvider(): array
    {
        return [
            [null],
            [new StringMapper()],
            [[]],
        ];
    }



    public function deserializeDataProvider(): array
    {
        return $this->serializeDataProvider();
    }

    public function deserializeInvalidDataProvider(): array
    {
        return $this->serializeInvalidDataProvider();
    }

    protected function getMapper(): MapperInterface
    {
        return new StringMapper();
    }
}
