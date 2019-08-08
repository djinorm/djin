<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 21.06.2017 23:19
 */

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\StubModelInterface;

class TestStubModel implements StubModelInterface, ModelInterface
{

    protected $id;

    public function __construct()
    {
        $this->id = new Id();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public static function getModelName(): string
    {
        return 'stub';
    }
}