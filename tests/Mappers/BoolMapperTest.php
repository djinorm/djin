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
        $mapper = new BoolMapper('value', 'value');

        $this->assertHydrated('', false, $mapper);
        $this->assertHydrated(0, false, $mapper);
        $this->assertHydrated(null, false, $mapper);
        $this->assertHydrated(false, false, $mapper);
        $this->assertHydrated('false', false, $mapper);

        $this->assertHydrated(1, true, $mapper);
        $this->assertHydrated(true, true, $mapper);
        $this->assertHydrated('1', true, $mapper);
        $this->assertHydrated('true', true, $mapper);
    }

    public function testExtract()
    {
        $mapper = new BoolMapper('value', 'value');

        $this->assertExtracted('', false, $mapper);
        $this->assertExtracted(0, false, $mapper);
        $this->assertExtracted(null, false, $mapper);
        $this->assertExtracted(false, false, $mapper);
        $this->assertExtracted('false', false, $mapper);

        $this->assertExtracted(1, true, $mapper);
        $this->assertExtracted(true, true, $mapper);
        $this->assertExtracted('1', true, $mapper);
        $this->assertExtracted('true', true, $mapper);
    }

}
