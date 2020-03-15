<?php
/**
 * Created for djin
 * Date: 15.03.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Helpers;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel_1;
use DjinORM\Djin\Model\Link;
use PHPUnit\Framework\TestCase;

class IdHelperTest extends TestCase
{

    public function idDataProvider(): array
    {
        return [
            [new TestModel_1(1), '1'],
            [new Link('model', 2), '2'],
            [new Id(3), '3'],
            [4, '4'],
            [null, null],
        ];
    }

    /**
     * @dataProvider idDataProvider
     * @param $input
     * @param $output
     */
    public function testScalarizeOne($input, $output)
    {
        $this->assertSame(
            $output,
            IdHelper::scalarizeOne($input)
        );
    }

    /**
     * @dataProvider idDataProvider
     * @param $input
     * @param $output
     */
    public function testScalarizeMany($input, $output)
    {
        $this->assertSame(
            [$output],
            IdHelper::scalarizeMany([$input])
        );
    }
}
