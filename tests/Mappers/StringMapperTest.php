<?php
/**
 * Created for DjinORM.
 * Datetime: 31.10.2017 11:36
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;

class StringMapperTest extends ScalarMapperTestCase
{

    public function testGetFixtures()
    {
        $fixtures = [
            '0V1D2OJxaviY8rzGS0RK',
            'KqS8Gr19sEeA87WgQ01D',
            'ltIkmQxtW7fgsFiywArY',
            'lFXhUAyyi5gAqkl5FSm8',
            'zg3f5dDH78O6QA1oTc1n',
            'xLlvPL7DKhi62CfkQwIp',
            '9egIDzBp69woT1GBUY7U',
            'SSevqiSgFY0dNA6wdaph',
        ];

        $fixtures = array_map(function ($value) {
            return substr($value, 0, 10);
        }, $fixtures);

        $this->assertGetFixtures($fixtures);
    }

    public function testExtractMaxLength()
    {
        $mapper = new StringMapper('value', 'value', true, 3);
        $this->expectException(ExtractorException::class);
        $mapper->extract($this->testClassValue);
    }

    protected function getTestClassValue()
    {
        return 'qwerty';
    }

    protected function getMapperAllowNull(): ScalarMapper
    {
        return new StringMapper('value', 'value', true, 10);
    }

    protected function getMapperDisallowNull(): ScalarMapper
    {
        return new StringMapper('value', 'value', false, 10);
    }
}
