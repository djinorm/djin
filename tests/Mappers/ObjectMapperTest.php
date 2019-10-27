<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 28.10.2019 2:00
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Mock\TestModel;

class ObjectMapperTest extends MapperTestCase
{

    public function serializeDataProvider(): array
    {
        return [
            [new TestModel(1, 2, '3'), ['id' => '1', 'otherId' => '2', 'custom' => '3']],
            [new TestModel(1, 2, null), ['id' => '1', 'otherId' => '2', 'custom' => null]],
        ];
    }

    public function serializeInvalidDataProvider(): array
    {
        return [
            [null],
            [1],
            ['string'],
            [[]],
            [new IdMapper()],
        ];
    }

    public function deserializeDataProvider(): array
    {
        return [
            [['id' => '1', 'otherId' => '2', 'custom' => '3'], new TestModel(1, 2, '3')],
            [['id' => '1', 'otherId' => '2', 'custom' => null], new TestModel(1, 2, null)],
            [['id' => '1', 'otherId' => '2'], new TestModel(1, 2, null)],
        ];
    }

    public function deserializeInvalidDataProvider(): array
    {
        return [
            [null],
            [1],
            ['string'],
            [[]],
            [new IdMapper()],
            [['id' => '1', 'otherId' => null, 'custom' => null]],
            [['id' => null, 'otherId' => null, 'custom' => null]],
        ];
    }

    protected function getMapper(): MapperInterface
    {
        return new ObjectMapper(
            TestModel::class,
            [
                'id' => new IdMapper(),
                'otherId' => new IdMapper(),
                'custom' => new NullOrMapper(new StringMapper()),
            ]
        );
    }
}
