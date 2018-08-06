<?php
/**
 * Created for DjinORM.
 * Datetime: 10.11.2017 15:30
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class NestedArrayMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $expected = [
            '__1__' => new TestModel(1, 'first'),
            '__2__' => new TestModel(2, 'second'),
            '__3__' => null
        ];
        $input = [
            '__1__' => [
                'id' => 1,
                'otherId' => 'first',
            ],
            '__2__' => [
                'id' => 2,
                'otherId' => 'second',
            ],
            '__3__' => null
        ];

        $this->assertHydrated($expected, $input, $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->getMapperDisallowNull()->hydrate([], $this->testClass);
    }


    public function testExtract()
    {
        $input = [
            '__1__' => new TestModel(1, 'first'),
            '__2__' => new TestModel(2, 'second'),
            '__3__' => null,
        ];

        $expected = [
            '__1__' => [
                'id' => 1,
                'otherId' => 'first',
            ],
            '__2__' => [
                'id' => 2,
                'otherId' => 'second',
            ],
            '__3__' => null
        ];

        $this->assertExtracted($expected, $input, $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->testClass->setValue(null);
        $this->getMapperDisallowNull()->extract($this->testClass);
    }

    protected function getMapperAllowNull(): NestedArrayMapper
    {
        return new NestedArrayMapper('value', TestModel::class, [
            new IdMapper('id'),
            new IdMapper('otherId'),
        ], true);
    }

    protected function getMapperDisallowNull(): NestedArrayMapper
    {
        return new NestedArrayMapper('value', TestModel::class, [
            new IdMapper('id'),
            new IdMapper('otherId'),
        ], false);
    }
}
