<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 14:46
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mock\MappersHandler;


use DjinORM\Djin\Mock\TestModel;

class TestSubmodelMapper
{

    public $string;

    /** @var TestModel[] */
    public $indexedArrayOfModel;

    /** @var TestModel[] */
    public $associativeArrayOfModel;

    public function __construct()
    {
        $this->string = '__string';

        $this->indexedArrayOfModel = [
            new TestModel(1, 1111),
            new TestModel(2, 2222),
        ];

        $this->associativeArrayOfModel = [
            '_first' => new TestModel(1, 11111),
            '_second' => new TestModel(2, 22222),
        ];
    }

}