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
    private $mappers = [];

    /** @var MappersHandler */
    private $mappersHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mappers = [
            'id' => new IdMapper('id'),
            'string' => new StringMapper('string'),
            'indexedArrayOfString' => new ArrayMapper('indexedArrayOfString', 'indexedArrayOfString', true),
            'associativeArrayOfString' => new ArrayMapper('associativeArrayOfString', 'associativeArrayOfString', true),
            'indexedArrayOfModel' => new ArrayMapper(
                'indexedArrayOfModel',
                'db_indexedArrayOfModel',
                true,
                new MappersHandler(TestModel::class, [
                    'id' => new IdMapper('id'),
                    'otherId' => new IdMapper('otherId'),
                ])
            ),
            'associativeArrayOfModel' => new ArrayMapper(
                'associativeArrayOfModel',
                'db_associativeArrayOfModel',
                true,
                new MappersHandler(TestModel::class, [
                    'id' => new IdMapper('id'),
                    'otherId' => new IdMapper('otherId'),
                ])
            ),
            'sub' => new SubclassMapper('sub', 'db_sub', new MappersHandler(TestSubmodelMapper::class, [
                'string' => new StringMapper('string'),
                new ArrayMapper(
                    'indexedArrayOfModel',
                    'db_indexedArrayOfModel',
                    true,
                    new MappersHandler(TestModel::class, [
                        'id' => new IdMapper('id'),
                        'otherId' => new IdMapper('otherId'),
                    ])
                ),
                new ArrayMapper(
                    'associativeArrayOfModel',
                    'db_associativeArrayOfModel',
                    true,
                    new MappersHandler(TestModel::class, [
                        'id' => new IdMapper('id'),
                        'otherId' => new IdMapper('otherId'),
                    ])
                ),
            ]))
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

    public function testGetModelPropertiesToDbAliases()
    {
        $expected = [
            'id' => 'id',
            'string' => 'string',

            'indexedArrayOfString' => 'indexedArrayOfString',
            'associativeArrayOfString' => 'associativeArrayOfString',

            'indexedArrayOfModel' => 'db_indexedArrayOfModel',
            'indexedArrayOfModel.id' => 'db_indexedArrayOfModel.id',
            'indexedArrayOfModel.otherId' => 'db_indexedArrayOfModel.otherId',

            'associativeArrayOfModel' => 'db_associativeArrayOfModel',
            'associativeArrayOfModel.id' => 'db_associativeArrayOfModel.id',
            'associativeArrayOfModel.otherId' => 'db_associativeArrayOfModel.otherId',

            'sub' => 'db_sub',
            'sub.string' => 'db_sub.string',

            'sub.indexedArrayOfModel' => 'db_sub.db_indexedArrayOfModel',
            'sub.indexedArrayOfModel.id' => 'db_sub.db_indexedArrayOfModel.id',
            'sub.indexedArrayOfModel.otherId' => 'db_sub.db_indexedArrayOfModel.otherId',

            'sub.associativeArrayOfModel' => 'db_sub.db_associativeArrayOfModel',
            'sub.associativeArrayOfModel.id' => 'db_sub.db_associativeArrayOfModel.id',
            'sub.associativeArrayOfModel.otherId' => 'db_sub.db_associativeArrayOfModel.otherId',
        ];

        $this->assertEquals($expected, $this->mappersHandler->getModelPropertiesToDbAliases());
    }

    public function testGetModelPropertyToDbAlias()
    {
        $this->assertEquals(
            'db_sub.db_indexedArrayOfModel.id',
            $this->mappersHandler->getModelPropertyToDbAlias('sub.indexedArrayOfModel.id')
        );
    }

    public function testGetDbAliasesToModelProperties()
    {
        $expected = [
            'id' => 'id',
            'string' => 'string',

            'indexedArrayOfString' => 'indexedArrayOfString',
            'associativeArrayOfString' => 'associativeArrayOfString',

            'indexedArrayOfModel' => 'db_indexedArrayOfModel',
            'indexedArrayOfModel.id' => 'db_indexedArrayOfModel.id',
            'indexedArrayOfModel.otherId' => 'db_indexedArrayOfModel.otherId',

            'associativeArrayOfModel' => 'db_associativeArrayOfModel',
            'associativeArrayOfModel.id' => 'db_associativeArrayOfModel.id',
            'associativeArrayOfModel.otherId' => 'db_associativeArrayOfModel.otherId',

            'sub' => 'db_sub',
            'sub.string' => 'db_sub.string',

            'sub.indexedArrayOfModel' => 'db_sub.db_indexedArrayOfModel',
            'sub.indexedArrayOfModel.id' => 'db_sub.db_indexedArrayOfModel.id',
            'sub.indexedArrayOfModel.otherId' => 'db_sub.db_indexedArrayOfModel.otherId',

            'sub.associativeArrayOfModel' => 'db_sub.db_associativeArrayOfModel',
            'sub.associativeArrayOfModel.id' => 'db_sub.db_associativeArrayOfModel.id',
            'sub.associativeArrayOfModel.otherId' => 'db_sub.db_associativeArrayOfModel.otherId',
        ];
        $expected = array_flip($expected);
        $this->assertEquals($expected, $this->mappersHandler->getDbAliasesToModelProperties());
    }

    public function testGetDbAliasToModelProperty()
    {
        $this->assertEquals(
            'sub.indexedArrayOfModel.id',
            $this->mappersHandler->getDbAliasToModelProperty('db_sub.db_indexedArrayOfModel.id')
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
        $this->assertEquals($expected, $this->mappersHandler->extract($model));
    }

    public function testGetMapperByModelProperty()
    {
        $this->assertInstanceOf(
            SubclassMapper::class,
            $this->mappersHandler->getMapperByModelProperty('sub')
        );

        $this->assertInstanceOf(
            ArrayMapper::class,
            $this->mappersHandler->getMapperByModelProperty('sub.associativeArrayOfModel')
        );

        $this->assertInstanceOf(
            IdMapper::class,
            $this->mappersHandler->getMapperByModelProperty('sub.associativeArrayOfModel.otherId')
        );

    }

    public function testGetMapperByDbAlias()
    {
        $this->assertInstanceOf(
            SubclassMapper::class,
            $this->mappersHandler->getMapperByDbAlias('db_sub')
        );

        $this->assertInstanceOf(
            ArrayMapper::class,
            $this->mappersHandler->getMapperByDbAlias('db_sub.db_associativeArrayOfModel')
        );

        $this->assertInstanceOf(
            IdMapper::class,
            $this->mappersHandler->getMapperByDbAlias('db_sub.db_associativeArrayOfModel.otherId')
        );

    }

}
