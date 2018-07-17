<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 17:44
 */

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;

class TestModel implements ModelInterface
{
    use ModelTrait;

    public $id;
    protected $otherId;
    protected $custom;

    public function __construct($id = null, $otherId = null)
    {
        $this->id = new Id($id);
        $this->otherId = new Id($otherId);
    }

    public function getOtherId()
    {
        return $this->otherId;
    }

    public function setOtherModel(TestModel $model)
    {
        $this->otherId = $model->getId();
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public static function getModelName(): string
    {
        return 'test-model';
    }
}