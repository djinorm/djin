<?php
/**
 * Created for DjinORM.
 * Datetime: 30.10.2017 14:24
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\TestHelpers;


class MockForMapperTest
{

    public $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getScalarValue()
    {
        return $this->value;
    }

}