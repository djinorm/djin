<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 14:01
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\TestHelpers;


use DjinORM\Djin\Mappers\MapperInterface;
use PHPUnit\Framework\TestCase;

abstract class MapperTestCase extends TestCase
{

    protected $testClass;


    public function setUp()
    {
        $this->testClass = new class() {
            protected $value;

            public function __construct($value = null)
            {
                $this->value = $value;
            }

            public function getValue()
            {
                return $this->value;
            }

            public function setValue($value)
            {
                $this->value = $value;
            }
        };
    }

    public function assertHydrated($scalarValue, $hydratedValue, MapperInterface $mapper)
    {
        $mapper->hydrate(['value' => $scalarValue], $this->testClass);
        $this->assertEquals($hydratedValue, $this->testClass->getValue());
    }

    public function assertExtracted($value, $extractedValue, MapperInterface $mapper)
    {
        $this->testClass->setValue($value);
        $this->assertEquals(
            ['value' => $extractedValue],
            $mapper->extract($this->testClass)
        );
    }

    abstract public function testHydrate();
    abstract public function testExtract();

}