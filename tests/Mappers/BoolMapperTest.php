<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 07.09.2019 17:57
 */

namespace DjinORM\Djin\Mappers;

use PHPUnit\Framework\TestCase;

class BoolMapperTest extends TestCase
{

    /** @var BoolMapper */
    private $mapper;

    protected function setUp()
    {
        parent::setUp();
        $this->mapper = new BoolMapper();
    }

    public function extractDataProvider()
    {
        return [
            ['complex' => true, 'data' => true],
            ['complex' => false, 'data' => false],
        ];
    }

    /**
     * @param $complex
     * @param $data
     * @dataProvider extractDataProvider
     */
    public function testExtract($complex, $data)
    {
        $this->assertEquals(
            $data,
            $this->mapper->extract($complex)
        );
    }

    public function hydrateDataProvider()
    {
        return [
            ['data' => true, 'complex' => true],
            ['data' => false, 'complex' => false],
            ['data' => 1, 'complex' => true],
            ['data' => 0, 'complex' => false],
        ];
    }

    /**
     * @param $data
     * @param $complex
     * @dataProvider hydrateDataProvider
     */
    public function testHydrate($data, $complex)
    {
        $this->assertEquals(
            $data,
            $this->mapper->extract($complex)
        );
    }
}
