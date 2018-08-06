<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 14:45
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class FloatMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated(0, '0', $this->getMapperAllowNull());
        $this->assertHydrated(10.5, '10.5', $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull());
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted(0, 0, $this->getMapperAllowNull());
        $this->assertExtracted(10.5, 10.5, $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    protected function getMapperAllowNull(): FloatMapper
    {
        return new FloatMapper('value',  true);
    }

    protected function getMapperDisallowNull(): FloatMapper
    {
        return new FloatMapper('value', false);
    }
}
