<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:42
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class NestedMapperTest extends MapperTestCase
{

    protected $testSubClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testSubClass = new class() {
            public $id;
            public $name;
            public $datetime;
        };
    }

    public function testHydrate()
    {
        $this->testSubClass->id = new Id(1);
        $this->testSubClass->name = 'Tony';
        $this->testSubClass->datetime = null;

        $expected = $this->getMapper()->hydrate([
            'value' => [
                'id' => 1,
                'name' => 'Tony',
                'datetime' => null
            ],
        ], $this->testClass);

        $this->assertEquals($expected, $this->testClass->getValue());
    }

    public function testExtract()
    {
        $this->testSubClass->id = new Id(1);
        $this->testSubClass->name = 'Tony';
        $this->testSubClass->datetime = null;

        $this->testClass->setValue($this->testSubClass);

        $expected = [
            'value' => [
                'id' => 1,
                'name' => 'Tony',
                'datetime' => null
            ],
        ];

        $this->assertEquals($expected, $this->getMapper()->extract($this->testClass));
    }

    protected function getMapper(): NestedMapper
    {
        return new NestedMapper('value', get_class($this->testSubClass), [
            new IdMapper('id'),
            new StringMapper('name'),
            new DatetimeMapper('datetime', true)
        ], false);
    }

}
