<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 11:30
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class BoolMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(false, '', $this->allowNullMapper());
        $this->assertHydrated(false, 0, $this->allowNullMapper());
        $this->assertHydrated(null, null, $this->allowNullMapper());
        $this->assertHydrated(false, false, $this->allowNullMapper());
        $this->assertHydrated(true, 'false', $this->allowNullMapper());

        $this->assertHydrated(true, 1, $this->allowNullMapper());
        $this->assertHydrated(true, true, $this->allowNullMapper());
        $this->assertHydrated(true, '1', $this->allowNullMapper());
        $this->assertHydrated(true, 'true', $this->allowNullMapper());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->disallowNullMapper());
    }

    public function testExtract()
    {
        $this->assertExtracted(false, '', $this->allowNullMapper());
        $this->assertExtracted(false, 0, $this->allowNullMapper());
        $this->assertExtracted(null, null, $this->allowNullMapper());
        $this->assertExtracted(false, false, $this->allowNullMapper());
        $this->assertExtracted(true, 'false', $this->allowNullMapper());

        $this->assertExtracted(true, 1, $this->allowNullMapper());
        $this->assertExtracted(true, true, $this->allowNullMapper());
        $this->assertExtracted(true, '1', $this->allowNullMapper());
        $this->assertExtracted(true, 'true', $this->allowNullMapper());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->disallowNullMapper());
    }

    public function allowNullMapper(): BoolMapper
    {
        return new BoolMapper('value', true, 'value');
    }

    public function disallowNullMapper(): BoolMapper
    {
        return new BoolMapper('value', false, 'value');
    }

}
