<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 14:31
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Handler;

use DjinORM\Djin\Mappers\ArrayMapper;
use DjinORM\Djin\Mappers\IdMapper;
use DjinORM\Djin\Mappers\NestedArrayMapper;
use DjinORM\Djin\Mappers\StringMapper;
use DjinORM\Djin\Mappers\NestedMapper;
use DjinORM\Djin\Mock\MappersHandler\TestModelMappersHandler;
use DjinORM\Djin\Mock\MappersHandler\TestSubmodelMapper;
use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class MappersHandlerTest extends TestCase
{

    /** @var array */
    private $mappers = [];

    /** @var MappersHandler */
    private $mappersHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mappers = [
            'id' => new IdMapper('id'),
            'string' => new StringMapper('string'),
            'indexedArrayOfString' => new ArrayMapper('indexedArrayOfString', true),
            'associativeArrayOfString' => new ArrayMapper('associativeArrayOfString', true),
            'indexedArrayOfModel' => new NestedArrayMapper(
                'indexedArrayOfModel',
                TestModel::class,
                [
                    'id' => new IdMapper('id'),
                    'otherId' => new IdMapper('otherId'),
                ],
                true
            ),
            'associativeArrayOfModel' => new NestedArrayMapper(
                'associativeArrayOfModel',
                TestModel::class,
                [
                    'id' => new IdMapper('id'),
                    'otherId' => new IdMapper('otherId'),
                ],
                true
            ),
            'sub' => new NestedMapper('sub', TestSubmodelMapper::class, [
                'string' => new StringMapper('string'),
                new NestedArrayMapper(
                    'indexedArrayOfModel',
                    TestModel::class,
                    [
                        'id' => new IdMapper('id'),
                        'otherId' => new IdMapper('otherId'),
                    ],
                    true
                ),
                new NestedArrayMapper(
                    'associativeArrayOfModel',
                    TestModel::class,
                    [
                        'id' => new IdMapper('id'),
                        'otherId' => new IdMapper('otherId'),
                    ],
                    true
                ),
            ], false)
        ];
        $this->mappersHandler = new MappersHandler(TestModelMappersHandler::class, $this->mappers);
    }

    public function testGetModelClassName()
    {
        $this->assertEquals(TestModelMappersHandler::class, $this->mappersHandler->getModelClassName());
    }

    public function testGetMappers()
    {
        $this->assertEquals($this->mappers, $this->mappersHandler->getMappers());
    }

    public function testHydrateArray()
    {
        $expected = new TestModelMappersHandler();
        $data = [
            'id' => 1,
            'string' => '_string',
            'indexedArrayOfString' => [
                '_string_1',
                '_string_2',
                '_string_3',
            ],
            'associativeArrayOfString' => [
                'first' => '_string_1',
                'second' => '_string_2',
                'third' => '_string_3',
            ],
            'indexedArrayOfModel' => [
                [
                    'id' => 1,
                    'otherId' => 11
                ],
                [
                    'id' => 2,
                    'otherId' => 22
                ],
            ],
            'associativeArrayOfModel' => [
                'first' => [
                    'id' => 1,
                    'otherId' => 111
                ],
                'second' => [
                    'id' => 2,
                    'otherId' => 222
                ],
            ],
            'sub' => [
                'string' => '__string',
                'indexedArrayOfModel' => [
                    [
                        'id' => 1,
                        'otherId' => 1111
                    ],
                    [
                        'id' => 2,
                        'otherId' => 2222
                    ],
                ],
                'associativeArrayOfModel' => [
                    '_first' => [
                        'id' => 1,
                        'otherId' => 11111
                    ],
                    '_second' => [
                        'id' => 2,
                        'otherId' => 22222
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $this->mappersHandler->hydrate($data));
    }

    public function testExtractArray()
    {
        $expected = [
            'id' => 1,
            'string' => '_string',
            'indexedArrayOfString' => [
                '_string_1',
                '_string_2',
                '_string_3',
            ],
            'associativeArrayOfString' => [
                'first' => '_string_1',
                'second' => '_string_2',
                'third' => '_string_3',
            ],
            'indexedArrayOfModel' => [
                [
                    'id' => 1,
                    'otherId' => 11
                ],
                [
                    'id' => 2,
                    'otherId' => 22
                ],
            ],
            'associativeArrayOfModel' => [
                'first' => [
                    'id' => 1,
                    'otherId' => 111
                ],
                'second' => [
                    'id' => 2,
                    'otherId' => 222
                ],
            ],
            'sub' => [
                'string' => '__string',
                'indexedArrayOfModel' => [
                    [
                        'id' => 1,
                        'otherId' => 1111
                    ],
                    [
                        'id' => 2,
                        'otherId' => 2222
                    ],
                ],
                'associativeArrayOfModel' => [
                    '_first' => [
                        'id' => 1,
                        'otherId' => 11111
                    ],
                    '_second' => [
                        'id' => 2,
                        'otherId' => 22222
                    ],
                ],
            ],
        ];
        $model = new TestModelMappersHandler();
        $this->assertEquals($expected, $this->mappersHandler->extract($model));
    }

    public function testGetMapperByModelProperty()
    {
        $this->assertInstanceOf(
            NestedMapper::class,
            $this->mappersHandler->getMapperByProperty('sub')
        );

        $this->assertInstanceOf(
            NestedArrayMapper::class,
            $this->mappersHandler->getMapperByProperty('sub.associativeArrayOfModel')
        );

        $this->assertInstanceOf(
            IdMapper::class,
            $this->mappersHandler->getMapperByProperty('sub.associativeArrayOfModel.otherId')
        );

        $this->assertNull(
            $this->mappersHandler->getMapperByProperty('indexedArrayOfString.0')
        );

        $this->assertNull(
            $this->mappersHandler->getMapperByProperty('qwerty')
        );

    }

}
