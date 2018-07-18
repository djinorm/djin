<?php
/**
 * Created for DjinORM.
 * Datetime: 10.11.2017 15:30
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Mappers\Handler\MappersHandler;
use DjinORM\Djin\Mappers\Notations\ArrayNotation;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class ArrayMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated(null, '', $this->getMapperAllowNull());

        $this->assertHydrated([], [], $this->getMapperDisallowNull());
        $this->assertHydrated([1, 2, 3], [1, 2, 3], $this->getMapperDisallowNull());
        $this->assertHydrated([1, 2, 3, 4 => [5, 6]], [1, 2, 3, 4 => [5, 6]], $this->getMapperDisallowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull());
    }

    public function testHydrateNested()
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

        $this->assertHydrated($expected, $input, $this->getNestedMapper());
        $this->assertHydrated($expected, $input, $this->getNestedMapper());
    }

    public function testExtract()
    {
         $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted(null, '', $this->getMapperAllowNull());

        $this->assertExtracted([], [], $this->getMapperAllowNull());
        $this->assertExtracted([1, 2, 3], [1, 2, 3], $this->getMapperAllowNull());
        $this->assertExtracted([1, 2, 3, 4 => [5, 6]], [1, 2, 3, 4 => [5, 6]], $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    public function testExtractNested()
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

        $this->assertExtracted($expected, $input, $this->getNestedMapper());
        $this->assertExtracted($expected, $input, $this->getNestedMapper());
    }

    protected function getNestedMapper(): ArrayMapper
    {
        return new ArrayMapper('value', 'value', new ArrayNotation(), true, new MappersHandler(TestModel::class, [
            new IdMapper('id'),
            new IdMapper('otherId'),
        ]), true);
    }

    protected function getMapperAllowNull(): ArrayMapper
    {
        return new ArrayMapper('value', 'value', new ArrayNotation(), true);
    }

    protected function getMapperDisallowNull(): ArrayMapper
    {
        return new ArrayMapper('value', 'value', new ArrayNotation(), false);
    }
}
