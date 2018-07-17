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
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class ArrayMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull(true));
        $this->assertHydrated(null, '', $this->getMapperAllowNull(true));

        $this->assertHydrated([], '[]', $this->getMapperDisallowNull(true));
        $this->assertHydrated([1, 2, 3], '[1, 2, 3]', $this->getMapperDisallowNull(true));
        $this->assertHydrated([1, 2, 3, 4 => [5, 6]], '{"0": 1, "1": 2, "2": 3, "4": [5, 6]}', $this->getMapperDisallowNull(true));

        $this->assertHydrated(null, null, $this->getMapperAllowNull(false));
        $this->assertHydrated(null, null, $this->getMapperAllowNull(false));

        $this->assertHydrated([], [], $this->getMapperDisallowNull(false));
        $this->assertHydrated([1, 2, 3], [1, 2, 3], $this->getMapperDisallowNull(false));
        $this->assertHydrated([1, 2, 3, 4 => [5, 6]], [1, 2, 3, 4 => [5, 6]], $this->getMapperDisallowNull(false));

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull(true));
    }

    public function testHydrateParseError()
    {
        $this->expectException(HydratorException::class);
        $this->expectExceptionCode(1);
        $this->assertHydrated([], 'qwerty', $this->getMapperDisallowNull(true));
    }

    public function testHydrateNested()
    {
        $expected = [
            '__1__' => new TestModel(1, 'first'),
            '__2__' => new TestModel(2, 'second'),
            '__3__' => null
        ];
        $input = '{"__1__": {"id": 1, "otherId": "first"}, "__2__": {"id": 2, "otherId": "second"}, "__3__": null}';

        $this->assertHydrated($expected, $input, $this->getNestedMapper(true));
        $this->assertHydrated($expected, json_decode($input, true), $this->getNestedMapper(false));
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull(true));
        $this->assertExtracted(null, '', $this->getMapperAllowNull(true));

        $this->assertExtracted('[]', [], $this->getMapperAllowNull(true));
        $this->assertExtracted('[1,2,3]', [1, 2, 3], $this->getMapperAllowNull(true));
        $this->assertExtracted('{"0":1,"1":2,"2":3,"4":[5,6]}', [1, 2, 3, 4 => [5, 6]], $this->getMapperAllowNull(true));

        $this->assertExtracted(null, null, $this->getMapperAllowNull(false));
        $this->assertExtracted(null, null, $this->getMapperAllowNull(false));

        $this->assertExtracted([], [], $this->getMapperAllowNull(false));
        $this->assertExtracted([1, 2, 3], [1, 2, 3], $this->getMapperAllowNull(false));
        $this->assertExtracted([1, 2, 3, 4 => [5, 6]], [1, 2, 3, 4 => [5, 6]], $this->getMapperAllowNull(false));

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull(true));
    }

    public function testExtractParseError()
    {
        $this->expectException(ExtractorException::class);
        $this->expectExceptionCode(1);

        /** @noinspection PhpUndefinedVariableInspection */
        $array = ['key' => &$array];

        $this->assertExtracted([], $array, $this->getMapperDisallowNull(true));
    }

    public function testExtractNested()
    {
        $input = [
            '__1__' => new TestModel(1, 'first'),
            '__2__' => new TestModel(2, 'second'),
            '__3__' => null,
        ];

        $expected = '{"__1__":{"id":1,"otherId":"first"},"__2__":{"id":2,"otherId":"second"},"__3__":null}';

        $this->assertExtracted($expected, $input, $this->getNestedMapper(true));
        $this->assertExtracted(json_decode($expected, true), $input, $this->getNestedMapper(false));
    }

    protected function getNestedMapper(bool $asJson): ArrayMapper
    {
        return new ArrayMapper('value', 'value', true, $asJson, new MappersHandler(TestModel::class, [
            new IdMapper('id'),
            new IdMapper('otherId'),
        ]), true);
    }

    protected function getMapperAllowNull(bool $asJson): ArrayMapper
    {
        return new ArrayMapper('value', 'value', true, $asJson);
    }

    protected function getMapperDisallowNull(bool $asJson): ArrayMapper
    {
        return new ArrayMapper('value', 'value', false, $asJson);
    }
}
