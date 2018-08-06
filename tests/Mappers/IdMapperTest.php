<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 17:16
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class IdMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated(new Id(0), '0', $this->getMapperAllowNull());
        $this->assertHydrated(new Id(10), '10', $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull());
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted(0, new Id(0), $this->getMapperAllowNull());
        $this->assertExtracted(10, new Id(10), $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    protected function getMapperAllowNull(): IdMapper
    {
        return new IdMapper('value', true);
    }

    protected function getMapperDisallowNull(): IdMapper
    {
        return new IdMapper('value', false);
    }
}
