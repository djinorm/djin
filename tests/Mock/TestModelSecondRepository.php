<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 18:44
 */

namespace DjinORM\Djin\Mock;


class TestModelSecondRepository extends TestModelRepository
{

    /**
     * Сообщает, может ли репозиторий откатить изменения. Если да, то
     * @see ModelManager сохранит эту модель одной из первых
     * @return bool
     */
    public function isTransactional(): bool
    {
        return false;
    }

    public static function getModelClass(): string
    {
        return TestSecondModel::class;
    }

}