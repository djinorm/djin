<?php
/**
 * Created for DjinORM.
 * Datetime: 13.12.2018 17:50
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Mock\TestSecondModel;
use DjinORM\Djin\Mock\TestStubModel;
use DjinORM\Djin\TestHelpers\MapperTestCase;
use ReflectionProperty;

class DeepIdentityMapperTest extends MapperTestCase
{

    private $objects;

    private $data = [];

    /** @var DeepIdentityMapper */
    private $mapperAllowNull;

    /** @var DeepIdentityMapper */
    private $mapperDisallowNull;

    /** @var DeepIdentityMapper */
    private $mapperNotAllClasses;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objects = [
            new TestModel(1, null, [
                new TestModel(null, null, [
                    'first' => 1,
                    'second' => 2,
                    'third' => null,
                ]),
                new TestModel(null, 1, [
                    new TestSecondModel(3, 4, [
                        new TestModel(),
                        new TestSecondModel(),
                        new TestStubModel()
                    ])
                ]),
            ]),
        ];

        $this->data = [
            0 => [
                '___{identity}___' => 'model:test',
                'data' => [
                    'id' => [
                        '___{identity}___' => 'id',
                        'data' => [
                            'id' => 1,
                        ]
                    ],
                    'otherId' => [
                        '___{identity}___' => 'id',
                        'data' => [
                            'id' => null,
                        ]
                    ],
                    'custom' => [
                        0 => [
                            '___{identity}___' => 'model:test',
                            'data' => [
                                'id' => [
                                    '___{identity}___' => 'id',
                                    'data' => [
                                        'id' => null,
                                    ]
                                ],
                                'otherId' => [
                                    '___{identity}___' => 'id',
                                    'data' => [
                                        'id' => null,
                                    ]
                                ],
                                'custom' => [
                                    'first' => 1,
                                    'second' => 2,
                                    'third' => null,
                                ],
                            ]
                        ],
                        1 => [
                            '___{identity}___' => 'model:test',
                            'data' => [
                                'id' => [
                                    '___{identity}___' => 'id',
                                    'data' => [
                                        'id' => null,
                                    ]
                                ],
                                'otherId' => [
                                    '___{identity}___' => 'id',
                                    'data' => [
                                        'id' => 1,
                                    ]
                                ],
                                'custom' => [
                                    0 => [
                                        '___{identity}___' => 'model:test:second',
                                        'data' => [
                                            'id' => [
                                                '___{identity}___' => 'id',
                                                'data' => [
                                                    'id' => 3,
                                                ]
                                            ],
                                            'otherId' => [
                                                '___{identity}___' => 'id',
                                                'data' => [
                                                    'id' => 4,
                                                ]
                                            ],
                                            'custom' => [
                                                0 => [
                                                    '___{identity}___' => 'model:test',
                                                    'data' => [
                                                        'id' => [
                                                            '___{identity}___' => 'id',
                                                            'data' => [
                                                                'id' => null,
                                                            ]
                                                        ],
                                                        'otherId' => [
                                                            '___{identity}___' => 'id',
                                                            'data' => [
                                                                'id' => null,
                                                            ]
                                                        ],
                                                        'custom' => null,
                                                    ],
                                                ],
                                                1 => [
                                                    '___{identity}___' => 'model:test:second',
                                                    'data' => [
                                                        'id' => [
                                                            '___{identity}___' => 'id',
                                                            'data' => [
                                                                'id' => null,
                                                            ]
                                                        ],
                                                        'otherId' => [
                                                            '___{identity}___' => 'id',
                                                            'data' => [
                                                                'id' => null,
                                                            ]
                                                        ],
                                                        'custom' => null,
                                                    ],
                                                ],
                                                2 => [
                                                    '___{identity}___' => 'model:test:stub',
                                                    'data' => [],
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $this->mapperAllowNull = new DeepIdentityMapper('value',
            [
                TestModel::class => 'model:test',
                TestSecondModel::class => 'model:test:second',
                TestStubModel::class => 'model:test:stub',
            ],
            [
                ReflectionProperty::IS_PUBLIC,
                ReflectionProperty::IS_PROTECTED,
                ReflectionProperty::IS_PRIVATE,
            ],
            true
        );

        $this->mapperDisallowNull = new DeepIdentityMapper('value', [
            TestModel::class => 'model:test',
            TestSecondModel::class => 'model:test:second',
            TestStubModel::class => 'model:test:stub',
        ]);

        $this->mapperNotAllClasses = new DeepIdentityMapper('value', [
            TestModel::class => 'model:test',
            TestSecondModel::class => 'model:test:second',
        ]);

    }

    public function testConstructMismatchCount()
    {
        $this->expectException(InvalidArgumentException::class);
        new DeepIdentityMapper('value', [
            TestModel::class => 'model:test',
            TestSecondModel::class => 'model:test:second',
            TestStubModel::class => 'model:test:second',
        ]);
    }

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->mapperAllowNull);
        $this->assertHydrated($this->objects, $this->data, $this->mapperAllowNull);

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->mapperDisallowNull);
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->mapperAllowNull);
        $this->assertExtracted($this->data, $this->objects, $this->mapperAllowNull);

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->mapperDisallowNull);
    }

    public function testHydrateNotAllClasses()
    {
        $this->expectException(HydratorException::class);
        $this->assertHydrated($this->objects, $this->data, $this->mapperNotAllClasses);
    }

    public function testExtractNotAllClasses()
    {
        $this->expectException(ExtractorException::class);
        $this->assertExtracted($this->data, $this->objects, $this->mapperNotAllClasses);
    }

    public function testExtractNonExtractable()
    {
        $this->expectException(ExtractorException::class);
        $this->objects = [
            fopen(__FILE__, 'r')
        ];
        $this->assertExtracted($this->data, $this->objects, $this->mapperAllowNull);
    }

}
