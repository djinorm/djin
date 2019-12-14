<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 17:44
 */

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;

class TestModel_1 implements ModelInterface
{

    public $id;
    protected $otherId;
    protected $custom;

    public function __construct($id = null, $otherId = null, $custom = null)
    {
        $this->id = new Id($id);
        $this->otherId = new Id($otherId);
        $this->custom = $custom;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getOtherId()
    {
        return $this->otherId;
    }

    public function setOtherModel(TestModel_1 $model)
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