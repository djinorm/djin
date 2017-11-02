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

    public function assertHydrated($expected, $input, MapperInterface $mapper)
    {
        $mapper->hydrate(['value' => $input], $this->testClass);
        $this->assertEquals($expected, $this->testClass->getValue());
    }

    public function assertExtracted($expected, $input, MapperInterface $mapper)
    {
        $this->testClass->setValue($input);
        $this->assertEquals(
            ['value' => $expected],
            $mapper->extract($this->testClass)
        );
    }

    abstract public function testHydrate();
    abstract public function testExtract();

}