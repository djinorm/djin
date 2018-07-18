<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 14:31
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Handler;

use DjinORM\Djin\Mappers\ArrayMapper;
use DjinORM\Djin\Mappers\IdMapper;
use DjinORM\Djin\Mappers\StringMapper;
use DjinORM\Djin\Mappers\SubclassMapper;
use DjinORM\Djin\Mock\MappersHandler\TestModelMappersHandler;
use DjinORM\Djin\Mock\MappersHandler\TestSubmodelMapper;
use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class MappersHandlerTest extends TestCase
{

    /** @var array */
    private $arrayMappers = [];

    /** @var array */
    private $jsonMappers = [];

    /** @var MappersHandler */
    private $mappersHandlerArray;

    /** @var MappersHandler */
    private $mappersHandlerJson;

    protected function setUp(): void
    {
        parent::setUp();
        $this->arrayMappers = [
            new IdMapper('id'),
            new StringMapper('string'),
            new ArrayMapper('indexedArrayOfString', 'indexedArrayOfString', true, false),
            new ArrayMapper('associativeArrayOfString', 'associativeArrayOfString', true, false),
            new ArrayMapper(
                'indexedArrayOfModel',
                'db_indexedArrayOfModel',
                true,
                false,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new ArrayMapper(
                'associativeArrayOfModel',
                'db_associativeArrayOfModel',
                true,
                false,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new SubclassMapper('sub', 'db_sub', false, new MappersHandler(TestSubmodelMapper::class, [
                new StringMapper('string'),
                new ArrayMapper(
                    'indexedArrayOfModel',
                    'db_indexedArrayOfModel',
                    true,
                    false,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
                new ArrayMapper(
                    'associativeArrayOfModel',
                    'db_associativeArrayOfModel',
                    true,
                    false,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
            ]))
        ];

        $this->jsonMappers = [
            new IdMapper('id'),
            new StringMapper('string'),
            new ArrayMapper('indexedArrayOfString', 'indexedArrayOfString', true, true),
            new ArrayMapper('associativeArrayOfString', 'associativeArrayOfString', true, true),
            new ArrayMapper(
                'indexedArrayOfModel',
                'db_indexedArrayOfModel',
                true,
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new ArrayMapper(
                'associativeArrayOfModel',
                'db_associativeArrayOfModel',
                true,
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new SubclassMapper('sub', 'db_sub', true, new MappersHandler(TestSubmodelMapper::class, [
                new StringMapper('string'),
                new ArrayMapper(
                    'indexedArrayOfModel',
                    'db_indexedArrayOfModel',
                    true,
                    true,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
                new ArrayMapper(
                    'associativeArrayOfModel',
                    'db_associativeArrayOfModel',
                    true,
                    true,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
            ]))
        ];

        $this->mappersHandlerArray = new MappersHandler(TestModelMappersHandler::class, $this->arrayMappers);
        $this->mappersHandlerJson = new MappersHandler(TestModelMappersHandler::class, $this->jsonMappers);
    }

    public function testGetModelClassName()
    {
        $this->assertEquals(TestModelMappersHandler::class, $this->mappersHandlerArray->getModelClassName());
    }

    public function testGetMappers()
    {
        $this->assertEquals($this->arrayMappers, $this->mappersHandlerArray->getMappers());
    }

    public function testModelPropertiesToDbAliases()
    {
        $expected = [
            'id' => 'id',
            'string' => 'string',

            'indexedArrayOfString' => 'indexedArrayOfString',
            'associativeArrayOfString' => 'associativeArrayOfString',

            'indexedArrayOfModel.id' => 'db_indexedArrayOfModel.id',
            'indexedArrayOfModel.otherId' => 'db_indexedArrayOfModel.otherId',

            'associativeArrayOfModel.id' => 'db_associativeArrayOfModel.id',
            'associativeArrayOfModel.otherId' => 'db_associativeArrayOfModel.otherId',

            'sub.string' => 'db_sub.string',

            'sub.indexedArrayOfModel.id' => 'db_sub.db_indexedArrayOfModel.id',
            'sub.indexedArrayOfModel.otherId' => 'db_sub.db_indexedArrayOfModel.otherId',

            'sub.associativeArrayOfModel.id' => 'db_sub.db_associativeArrayOfModel.id',
            'sub.associativeArrayOfModel.otherId' => 'db_sub.db_associativeArrayOfModel.otherId',
        ];

        $this->assertEquals($expected, $this->mappersHandlerArray->modelPropertiesToDbAliases());
        $this->assertEquals($expected, $this->mappersHandlerJson->modelPropertiesToDbAliases());
    }

    public function testModelPropertyToDbAlias()
    {
        $this->assertEquals(
            'db_sub.db_indexedArrayOfModel.id',
            $this->mappersHandlerArray->modelPropertyToDbAlias('sub.indexedArrayOfModel.id')
        );

        $this->assertEquals(
            'db_sub.db_indexedArrayOfModel.id',
            $this->mappersHandlerJson->modelPropertyToDbAlias('sub.indexedArrayOfModel.id')
        );
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
            'db_indexedArrayOfModel' => [
                [
                    'id' => 1,
                    'otherId' => 11
                ],
                [
                    'id' => 2,
                    'otherId' => 22
                ],
            ],
            'db_associativeArrayOfModel' => [
                'first' => [
                    'id' => 1,
                    'otherId' => 111
                ],
                'second' => [
                    'id' => 2,
                    'otherId' => 222
                ],
            ],
            'db_sub' => [
                'string' => '__string',
                'db_indexedArrayOfModel' => [
                    [
                        'id' => 1,
                        'otherId' => 1111
                    ],
                    [
                        'id' => 2,
                        'otherId' => 2222
                    ],
                ],
                'db_associativeArrayOfModel' => [
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
        $this->assertEquals($expected, $this->mappersHandlerArray->hydrate($data));
    }

    public function testHydrateJson()
    {
        $expected = new TestModelMappersHandler();
        $data = [
            'id' => 1,
            'string' => '_string',
            'indexedArrayOfString' => json_encode([
                '_string_1',
                '_string_2',
                '_string_3',
            ]),
            'associativeArrayOfString' => json_encode([
                'first' => '_string_1',
                'second' => '_string_2',
                'third' => '_string_3',
            ]),
            'db_indexedArrayOfModel' => json_encode([
                [
                    'id' => 1,
                    'otherId' => 11
                ],
                [
                    'id' => 2,
                    'otherId' => 22
                ],
            ]),
            'db_associativeArrayOfModel' => json_encode([
                'first' => [
                    'id' => 1,
                    'otherId' => 111
                ],
                'second' => [
                    'id' => 2,
                    'otherId' => 222
                ],
            ]),
            'db_sub' => json_encode([
                'string' => '__string',
                'db_indexedArrayOfModel' => json_encode([
                    [
                        'id' => 1,
                        'otherId' => 1111
                    ],
                    [
                        'id' => 2,
                        'otherId' => 2222
                    ],
                ]),
                'db_associativeArrayOfModel' => json_encode([
                    '_first' => [
                        'id' => 1,
                        'otherId' => 11111
                    ],
                    '_second' => [
                        'id' => 2,
                        'otherId' => 22222
                    ],
                ]),
            ]),
        ];
        $this->assertEquals($expected, $this->mappersHandlerJson->hydrate($data));
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
            'db_indexedArrayOfModel' => [
                [
                    'id' => 1,
                    'otherId' => 11
                ],
                [
                    'id' => 2,
                    'otherId' => 22
                ],
            ],
            'db_associativeArrayOfModel' => [
                'first' => [
                    'id' => 1,
                    'otherId' => 111
                ],
                'second' => [
                    'id' => 2,
                    'otherId' => 222
                ],
            ],
            'db_sub' => [
                'string' => '__string',
                'db_indexedArrayOfModel' => [
                    [
                        'id' => 1,
                        'otherId' => 1111
                    ],
                    [
                        'id' => 2,
                        'otherId' => 2222
                    ],
                ],
                'db_associativeArrayOfModel' => [
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
        $this->assertEquals($expected, $this->mappersHandlerArray->extract($model));
    }

    public function testExtractJson()
    {
        $expected = [
            'id' => 1,
            'string' => '_string',
            'indexedArrayOfString' => json_encode([
                '_string_1',
                '_string_2',
                '_string_3',
            ]),
            'associativeArrayOfString' => json_encode([
                'first' => '_string_1',
                'second' => '_string_2',
                'third' => '_string_3',
            ]),
            'db_indexedArrayOfModel' => json_encode([
                [
                    'id' => 1,
                    'otherId' => 11
                ],
                [
                    'id' => 2,
                    'otherId' => 22
                ],
            ]),
            'db_associativeArrayOfModel' => json_encode([
                'first' => [
                    'id' => 1,
                    'otherId' => 111
                ],
                'second' => [
                    'id' => 2,
                    'otherId' => 222
                ],
            ]),
            'db_sub' => json_encode([
                'string' => '__string',
                'db_indexedArrayOfModel' => json_encode([
                    [
                        'id' => 1,
                        'otherId' => 1111
                    ],
                    [
                        'id' => 2,
                        'otherId' => 2222
                    ],
                ]),
                'db_associativeArrayOfModel' => json_encode([
                    '_first' => [
                        'id' => 1,
                        'otherId' => 11111
                    ],
                    '_second' => [
                        'id' => 2,
                        'otherId' => 22222
                    ],
                ]),
            ]),
        ];
        $model = new TestModelMappersHandler();
        $this->assertEquals($expected, $this->mappersHandlerJson->extract($model));
    }

}
