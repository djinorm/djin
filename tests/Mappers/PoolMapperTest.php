<?php
/**
 * Created for djin
 * Date: 03.01.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Pool\FloatPool;
use DjinORM\Djin\Pool\IntPool;

class PoolMapperTest extends MapperTestCase
{

    public function serializeDataProvider(): array
    {
        return [
            [new FloatPool(100.1), ['current' => 100.1, 'pool' => 0.0]],
            [new FloatPool(100.0, 10.2), ['current' => 100.0, 'pool' => 10.2]],
        ];
    }

    public function serializeInvalidDataProvider(): array
    {
        return [
            [new IntPool(100)],
            ['hello'],
        ];
    }

    public function deserializeDataProvider(): array
    {
        return [
            [100.1, new FloatPool(100.1)],
            [0.0, new FloatPool(0.0)],
        ];
    }

    public function deserializeInvalidDataProvider(): array
    {
        return [
            ['hello'],
        ];
    }

    protected function getMapper(): MapperInterface
    {
        return new PoolMapper(
            FloatPool::class,
            new FloatMapper()
        );
    }
}
