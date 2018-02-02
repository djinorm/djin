<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 18:44
 */

namespace DjinORM\Djin\Mock;


class TestModelSecondRepository extends TestModelRepository
{

    public static function getModelClass(): string
    {
        return TestSecondModel::class;
    }

}