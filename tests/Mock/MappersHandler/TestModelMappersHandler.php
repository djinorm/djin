<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 14:38
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mock\MappersHandler;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;

class TestModelMappersHandler implements ModelInterface
{

    use ModelTrait;

    /** @var Id */
    public $id;

    /** @var string */
    public $string;

    /** @var array */
    public $indexedArrayOfString;

    /** @var array */
    public $associativeArrayOfString;

    /** @var TestModel[] */
    public $indexedArrayOfModel;

    /** @var TestModel[] */
    public $associativeArrayOfModel;

    /** @var TestSubmodelMapper */
    public $sub;

    public function __construct()
    {
        $this->id = new Id(1);
        $this->string = '_string';

        $this->indexedArrayOfString = [
            '_string_1',
            '_string_2',
            '_string_3',
        ];

        $this->associativeArrayOfString = [
            'first' => '_string_1',
            'second' => '_string_2',
            'third' => '_string_3',
        ];

        $this->indexedArrayOfModel = [
            new TestModel(1, 11),
            new TestModel(2, 22),
        ];

        $this->associativeArrayOfModel = [
            'first' => new TestModel(1, 111),
            'second' => new TestModel(2, 222),
        ];

        $this->sub = new TestSubmodelMapper();
    }
}