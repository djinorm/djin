<?php
/**
 * Created for djin
 * Datetime: 26.06.2019 19:21
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Mock\ObjectValue;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class ObjectValueMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated(new ObjectValue(10), 10, $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull());
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted(10, new ObjectValue(10), $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    protected function getMapperAllowNull(): ObjectValueMapper
    {
        return new ObjectValueMapper('value', ObjectValue::class, new IntMapper('myValue', true));
    }

    protected function getMapperDisallowNull(): ObjectValueMapper
    {
        return new ObjectValueMapper('value', ObjectValue::class, new IntMapper('myValue', false));
    }

}