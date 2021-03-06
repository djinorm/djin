<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 07.08.2017 2:33
 */

namespace DjinORM\Djin\Id;

use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{

    public function testGetNextId()
    {
        $model = new TestModel();
        $generator = new UuidGenerator();

        //6a737b8b-12de-4396-ad46-b5774099a8b5
        $regexp = '~^[a-z\d]{8}-([a-z\d]{4}-){3}[a-z\d]{12}$~';

        $ids = [];

        for ($i = 1; $i <= 100; $i++) {
            $id = $generator->getNextId($model);
            $ids[$id] = true;
            $this->assertTrue(preg_match($regexp, $id) == 1);
        }

        $this->assertCount(100, $ids);
    }

}
