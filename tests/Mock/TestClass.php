<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:30
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mock;


class TestClass
{
    protected $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}