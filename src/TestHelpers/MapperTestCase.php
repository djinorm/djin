<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 14:01
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\TestHelpers;


use DjinORM\Djin\Mappers\MapperInterface;
use DjinORM\Djin\Mock\TestClass;
use PHPUnit\Framework\TestCase;

abstract class MapperTestCase extends TestCase
{

    /** @var TestClass */
    protected $testClass;


    protected function setUp(): void
    {
        $this->testClass = new TestClass();
    }

    /**
     * @param $expected
     * @param $input
     * @param MapperInterface $mapper
     */
    public function assertHydrated($expected, $input, MapperInterface $mapper)
    {
        $mapper->hydrate(['value' => $input], $this->testClass);
        $this->assertEquals($expected, $this->testClass->getValue());
    }

    /**
     * @param $expected
     * @param $input
     * @param MapperInterface $mapper
     */
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