<?php


namespace DjinORM\Djin\Mock;


class TestRepo_2 extends Repository
{

    protected function getClassName(): string
    {
        return TestModel_2::class;
    }
}