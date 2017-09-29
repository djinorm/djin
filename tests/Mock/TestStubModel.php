<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 21.06.2017 23:19
 */

namespace DjinORM\Djin\tests\Mock;


use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;
use DjinORM\Djin\Model\StubModelInterface;

class TestStubModel implements StubModelInterface, ModelInterface
{

    use ModelTrait;

}