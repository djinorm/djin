<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 14:45
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


class FloatMapperTest extends ScalarMapperTestCase
{

    public function testGetFixtures()
    {
        $this->assertGetFixtures(range(0, 5, 0.5));
    }

    protected function getTestClassValue()
    {
        return 11.1;
    }

    protected function getMapperAllowNull(): ScalarMapper
    {
        return new FloatMapper('value', 'value', true);
    }

    protected function getMapperDisallowNull(): ScalarMapper
    {
        return new FloatMapper('value', 'value', false);
    }
}
