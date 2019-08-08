<?php
/**
 * Created for DjinORM.
 * Datetime: 31.10.2017 11:36
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class StringMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated('', '', $this->getMapperAllowNull());
        $this->assertHydrated('qwerty', 'qwerty', $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull());
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted('', '', $this->getMapperAllowNull());
        $this->assertExtracted('qwerty', 'qwerty', $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    public function testExtractMaxLength()
    {
        $mapper = new StringMapper('value', 3, true);
        $this->expectException(ExtractorException::class);
        $this->testClass->setValue('qwerty');
        $mapper->extract($this->testClass);
    }

    protected function getMapperAllowNull(): StringMapper
    {
        return new StringMapper('value', 10, true);
    }

    protected function getMapperDisallowNull(): StringMapper
    {
        return new StringMapper('value', 10, false);
    }

}