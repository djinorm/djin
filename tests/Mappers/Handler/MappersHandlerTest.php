<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 14:31
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Handler;

use DjinORM\Djin\Mappers\ArrayMapper;
use DjinORM\Djin\Mappers\IdMapper;
use DjinORM\Djin\Mappers\Notations\ArrayNotation;
use DjinORM\Djin\Mappers\Notations\DotNotation;
use DjinORM\Djin\Mappers\Notations\JsonNotation;
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

    /** @var array */
    private $dotMappers = [];

    /** @var MappersHandler */
    private $mappersHandlerArray;

    /** @var MappersHandler */
    private $mappersHandlerJson;

    /** @var MappersHandler */
    private $mappersHandlerDot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->arrayMappers = [
            new IdMapper('id'),
            new StringMapper('string'),
            new ArrayMapper('indexedArrayOfString', 'indexedArrayOfString', new ArrayNotation(), true),
            new ArrayMapper('associativeArrayOfString', 'associativeArrayOfString', new ArrayNotation(), true),
            new ArrayMapper(
                'indexedArrayOfModel',
                'db_indexedArrayOfModel',
                new ArrayNotation(),
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new ArrayMapper(
                'associativeArrayOfModel',
                'db_associativeArrayOfModel',
                new ArrayNotation(),
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new SubclassMapper('sub', 'db_sub', new ArrayNotation(), new MappersHandler(TestSubmodelMapper::class, [
                new StringMapper('string'),
                new ArrayMapper(
                    'indexedArrayOfModel',
                    'db_indexedArrayOfModel',
                    new ArrayNotation(),
                    true,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
                new ArrayMapper(
                    'associativeArrayOfModel',
                    'db_associativeArrayOfModel',
                    new ArrayNotation(),
                    true,
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
            new ArrayMapper('indexedArrayOfString', 'indexedArrayOfString', new JsonNotation(), true),
            new ArrayMapper('associativeArrayOfString', 'associativeArrayOfString', new JsonNotation(), true),
            new ArrayMapper(
                'indexedArrayOfModel',
                'db_indexedArrayOfModel',
                new JsonNotation(),
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new ArrayMapper(
                'associativeArrayOfModel',
                'db_associativeArrayOfModel',
                new JsonNotation(),
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new SubclassMapper('sub', 'db_sub', new JsonNotation(), new MappersHandler(TestSubmodelMapper::class, [
                new StringMapper('string'),
                new ArrayMapper(
                    'indexedArrayOfModel',
                    'db_indexedArrayOfModel',
                    new JsonNotation(),
                    true,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
                new ArrayMapper(
                    'associativeArrayOfModel',
                    'db_associativeArrayOfModel',
                    new JsonNotation(),
                    true,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
            ]))
        ];

        $this->dotMappers = [
            new IdMapper('id'),
            new StringMapper('string'),
            new ArrayMapper('indexedArrayOfString', 'indexedArrayOfString', new DotNotation(), true),
            new ArrayMapper('associativeArrayOfString', 'associativeArrayOfString', new DotNotation(), true),
            new ArrayMapper(
                'indexedArrayOfModel',
                'db_indexedArrayOfModel',
                new DotNotation(),
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new ArrayMapper(
                'associativeArrayOfModel',
                'db_associativeArrayOfModel',
                new DotNotation(),
                true,
                new MappersHandler(TestModel::class, [
                    new IdMapper('id'),
                    new IdMapper('otherId'),
                ])
            ),
            new SubclassMapper('sub', 'db_sub', new DotNotation(), new MappersHandler(TestSubmodelMapper::class, [
                new StringMapper('string'),
                new ArrayMapper(
                    'indexedArrayOfModel',
                    'db_indexedArrayOfModel',
                    new DotNotation(),
                    true,
                    new MappersHandler(TestModel::class, [
                        new IdMapper('id'),
                        new IdMapper('otherId'),
                    ])
                ),
                new ArrayMapper(
                    'associativeArrayOfModel',
                    'db_associativeArrayOfModel',
                    new DotNotation(),
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
        $this->mappersHandlerDot = new MappersHandler(TestModelMappersHandler::class, $this->dotMappers);
    }

    public function testGetModelClassName()
    {
        $this->assertEquals(TestModelMappersHandler::class, $this->mappersHandlerArray->getModelClassName());
        $this->assertEquals(TestModelMappersHandler::class, $this->mappersHandlerJson->getModelClassName());
        $this->assertEquals(TestModelMappersHandler::class, $this->mappersHandlerDot->getModelClassName());
    }

    public function testGetMappers()
    {
        $this->assertEquals($this->arrayMappers, $this->mappersHandlerArray->getMappers());
        $this->assertEquals($this->jsonMappers, $this->mappersHandlerJson->getMappers());
        $this->assertEquals($this->dotMappers, $this->mappersHandlerDot->getMappers());
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
        $this->assertEquals($expected, $this->mappersHandlerDot->modelPropertiesToDbAliases());
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

        $this->assertEquals(
            'db_sub.db_indexedArrayOfModel.id',
            $this->mappersHandlerDot->modelPropertyToDbAlias('sub.indexedArrayOfModel.id')
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

    public function testHydrateDot()
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
        $data = (new DotNotation())->encode($data);
        $this->assertEquals($expected, $this->mappersHandlerDot->hydrate($data));
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
