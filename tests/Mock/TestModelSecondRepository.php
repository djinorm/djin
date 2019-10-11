<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 18:44
 */

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Model\ModelInterface;

class TestModelSecondRepository extends TestModelRepo
{

    protected $throwException = false;

    public static function getModelClass(): string
    {
        return TestSecondModel::class;
    }

    public function throwExceptionOnSave(bool $throw)
    {
        $this->throwException = $throw;
    }

    public function save(ModelInterface $model)
    {
        parent::save($model);
        if ($this->throwException) {
            throw new \Exception('Testing exception');
        }
    }

}