<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 28.10.2019 2:15
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\SerializerException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mappers\Components\UnionRule;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Mock\TestSecondModel;
use Throwable;

class UnionMapperTest extends MapperTestCase
{

    public function serializeDataProvider(): array
    {
        return [
            ['string', 'string'],
            [1, 1],
            [
                new TestModel(1, 2, 3),
                ['id' => '1', 'otherId' => '2', 'custom' => 3, 'model' => 'TestModel'],
            ],
            [
                new TestSecondModel(1, 2, 'three'),
                ['id' => '1', 'otherId' => '2', 'custom' => 'three', 'model' => 'TestSecondModel'],
            ],
        ];
    }

    public function serializeInvalidDataProvider(): array
    {
        return [
            [1.1],
            [[]],
            [null],
            [false],
            [new Id(1)],
        ];
    }

    public function deserializeDataProvider(): array
    {
        return [
            ['string', 'string'],
            [1, 1],
            [
                ['id' => '1', 'otherId' => '2', 'custom' => 3, 'model' => 'TestModel'],
                new TestModel(1, 2, 3),
            ],
            [
                ['id' => '1', 'otherId' => '2', 'custom' => 'three', 'model' => 'TestSecondModel'],
                new TestSecondModel(1, 2, 'three'),
            ],
        ];
    }

    public function deserializeInvalidDataProvider(): array
    {
        return [
            [1.1],
            [[]],
            [null],
            [false],
            [new Id(1)],
            [['id' => '1', 'otherId' => '2', 'custom' => 3]]
        ];
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
        $string = new UnionRule(
            new StringMapper(),
            function ($complex, $serialized) {
                if (is_string($complex)) {
                    return $serialized;
                }

                throw new SerializerException('Only string serialization');
            },
            function ($serialized) {
                if (is_string($serialized)) {
                    return $serialized;
                }

                throw new SerializerException('Only string serialization');
            }
        );

        $int = new UnionRule(
            new IntMapper(),
            function ($complex, $serialized) {
                return $serialized;
            },
            function ($serialized) {
                if (is_int($serialized)) {
                    return $serialized;
                }

                throw new SerializerException('Only integer serialization');
            }
        );

        $testModel = new UnionRule(
            new ObjectMapper(
                TestModel::class,
                [
                    'id' => new IdMapper(),
                    'otherId' => new IdMapper(),
                    'custom' => new IntMapper(),
                ]
            ),
            function ($complex, $serialized) {
                $serialized['model'] = 'TestModel';
                return $serialized;
            },
            function ($serialized) {
                if ($serialized['model'] === 'TestModel') {
                    return $serialized;
                }

                throw new SerializerException('Only TestModel serialization');
            }
        );

        $testSecondModel = new UnionRule(
            new ObjectMapper(
                TestSecondModel::class,
                [
                    'id' => new IdMapper(),
                    'otherId' => new IdMapper(),
                    'custom' => new StringMapper(),
                ]
            ),
            function ($complex, $serialized) {
                $serialized['model'] = 'TestSecondModel';
                return $serialized;
            },
            function ($serialized) {
                if ($serialized['model'] === 'TestSecondModel') {
                    return $serialized;
                }

                throw new SerializerException('Only TestSecondModel serialization');
            }
        );


        return new UnionMapper([$string, $int, $testModel, $testSecondModel]);
    }
}
