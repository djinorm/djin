<?php
/**
 * Created for DjinORM.
 * Datetime: 18.07.2018 12:53
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;

use PHPUnit\Framework\TestCase;

class DotNotationTest extends TestCase
{

    /** @var NotationInterface */
    private $notation;

    /** @var array */
    private $encoded;

    /** @var array */
    private $decoded;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notation = new DotNotation();
        $this->decoded = [
            '__first__' => 1,
            '__second__' => 2,
            'indexed' => [1, 2, 3],
            'nested' => [
                'value_1' => 1,
                'value_2' => 2,
                'value_3' => 3,
                'value_4' => ['hello', 'world']
            ]
        ];
        $this->encoded = [
            '__first__' => 1,
            '__second__' => 2,
            'indexed.0' => 1,
            'indexed.1' => 2,
            'indexed.2' => 3,
            'nested.value_1' => 1,
            'nested.value_2' => 2,
            'nested.value_3' => 3,
            'nested.value_4.0' => 'hello',
            'nested.value_4.1' => 'world',
        ];
    }

    public function testEncode()
    {
        $this->assertEquals($this->encoded, $this->notation->encode($this->decoded));
    }

    public function testDecode()
    {
        $this->assertEquals($this->decoded, $this->notation->decode($this->encoded));
    }
}
