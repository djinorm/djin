<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 11:30
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\TestHelpers\MapperTestCase;

class BoolMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $mapper = new BoolMapper('value');

        $this->assertHydrated(false, '', $mapper);
        $this->assertHydrated(false, 0, $mapper);
        $this->assertHydrated(false, null, $mapper);
        $this->assertHydrated(false, false, $mapper);
        $this->assertHydrated(false, 'false', $mapper);

        $this->assertHydrated(true, 1, $mapper);
        $this->assertHydrated(true, true, $mapper);
        $this->assertHydrated(true, '1', $mapper);
        $this->assertHydrated(true, 'true', $mapper);
    }

    public function testExtract()
    {
        $mapper = new BoolMapper('value', 'value');

        $this->assertExtracted(false, '', $mapper, false);
        $this->assertExtracted(false, 0, $mapper, false);
        $this->assertExtracted(false, null, $mapper, false);
        $this->assertExtracted(false, false, $mapper, false);
        $this->assertExtracted(false, 'false', $mapper, false);

        $this->assertExtracted(true, 1, $mapper, false);
        $this->assertExtracted(true, true, $mapper, false);
        $this->assertExtracted(true, '1', $mapper, false);
        $this->assertExtracted(true, 'true', $mapper, false);
    }

}
