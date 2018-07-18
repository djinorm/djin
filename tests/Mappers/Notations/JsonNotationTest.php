<?php
/**
 * Created for DjinORM.
 * Datetime: 18.07.2018 12:59
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;

use PHPUnit\Framework\TestCase;

class JsonNotationTest extends TestCase
{

    /** @var NotationInterface */
    private $notation;

    /** @var string */
    private $encoded;

    /** @var array */
    private $decoded;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notation = new JsonNotation();
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
        $this->encoded = json_encode($this->decoded);
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
