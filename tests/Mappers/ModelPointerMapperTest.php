<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:27
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\Relation;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class ModelPointerMapperTest extends MapperTestCase
{

    protected $testSubClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testSubClass = new Relation('MyModelName', new Id(100));
    }

    public function testHydrate()
    {
        $expected = $this->getMapper()->hydrate([
            'value' => [
                'id' => 1,
                'model' => 'MyModelName',
            ],
        ], $this->testClass);

        $this->assertEquals($expected, $this->testClass->getValue());
    }

    public function testExtract()
    {
        $this->testClass->setValue($this->testSubClass);
        $expected = [
            'value' => [
                'id' => 100,
                'model' => 'MyModelName',
            ],
        ];

        $this->assertEquals($expected, $this->getMapper()->extract($this->testClass));
    }

    protected function getMapper(): RelationMapper
    {
        return new RelationMapper('value', false);
    }

}
