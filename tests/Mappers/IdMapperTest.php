<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 17:16
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestMapper;

class IdMapperTest extends ScalarMapperTestCase
{

    public function setUp()
    {
        $this->testClassValue = new class(new Id(777)) extends TestMapper{
            /** @var Id */
            public $value;

            public function getScalarValue()
            {
                return $this->value ? $this->value->toScalar() : null;
            }
        };

        $this->testClassNull = new class() extends TestMapper {
            /** @var Id */
            public $value;

            public function getScalarValue()
            {
                return $this->value ? $this->value->toScalar() : null;
            }
        };
    }

    public function testGetFixtures()
    {
        $this->assertGetFixtures(range(0, 9));
    }

    protected function getMapperAllowNull(): ScalarMapper
    {
        return new IdMapper('value', 'value', true);
    }

    protected function getMapperDisallowNull(): ScalarMapper
    {
        return new IdMapper('value', 'value', false);
    }

    protected function getTestClassValue()
    {
        return;
    }
}
