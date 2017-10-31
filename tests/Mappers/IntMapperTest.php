<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 15:44
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\TestHelpers\ScalarMapperTestCase;

class IntMapperTest extends ScalarMapperTestCase
{


    public function testGetFixtures()
    {
        $this->assertGetFixtures(range(0, 100, 10));
    }

    protected function getTestClassValue()
    {
        return 999;
    }

    protected function getMapperAllowNull(): ScalarMapper
    {
        return new IntMapper('value', 'value', true);
    }

    protected function getMapperDisallowNull(): ScalarMapper
    {
        return new IntMapper('value', 'value', false);
    }
}
