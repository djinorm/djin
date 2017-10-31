<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 14:01
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\TestHelpers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Mappers\ScalarMapper;
use DjinORM\Djin\Mock\TestMapper;
use PHPUnit\Framework\TestCase;

abstract class ScalarMapperTestCase extends TestCase
{

    /** @var TestMapper */
    protected $testClassValue;

    /** @var TestMapper */
    protected $testClassNull;

    public function setUp()
    {
        $this->testClassNull = new TestMapper();
        $this->testClassValue = new TestMapper($this->getTestClassValue());
    }

    public function testHydrateAllowNull()
    {
        $mapper = $this->getMapperAllowNull();
        $mapper->hydrate([], $this->testClassValue);
        $this->assertNull($this->testClassValue->getScalarValue());

        $mapper->hydrate(['value' => 7], $this->testClassValue);
        $this->assertEquals(7, $this->testClassValue->getScalarValue());

        $mapper->hydrate(['value' => null], $this->testClassValue);
        $this->assertNull($this->testClassValue->getScalarValue());
    }

    public function nullArrayProvider()
    {
        return [
            [[]],
            [['value' => null]],
        ];
    }

    /**
     * @dataProvider nullArrayProvider
     * @param array $array
     */
    public function testHydrateDisallowNull(array $array)
    {
        $this->expectException(HydratorException::class);
        $this->getMapperDisallowNull()->hydrate($array, $this->testClassValue);
    }

    public function testExtractAllowNull()
    {
        $mapper = $this->getMapperAllowNull();

        $this->assertEquals(
            ['value' => $this->testClassValue->getScalarValue()],
            $mapper->extract($this->testClassValue)
        );

        $this->assertEquals(
            ['value' => null],
            $mapper->extract($this->testClassNull)
        );
    }

    public function testExtractDisallowNull()
    {
        $mapper = $this->getMapperDisallowNull();
        $this->expectException(ExtractorException::class);
        $mapper->extract($this->testClassNull);
    }

    public function assertGetFixtures($expected)
    {
        $this->assertEquals(
            array_merge($expected, [null]),
            $this->getMapperAllowNull()->getFixtures()
        );

        $this->assertEquals(
            $expected,
            $this->getMapperDisallowNull()->getFixtures()
        );
    }

    abstract public function testGetFixtures();

    abstract protected function getTestClassValue();
    abstract protected function getMapperAllowNull(): ScalarMapper;
    abstract protected function getMapperDisallowNull(): ScalarMapper;

}