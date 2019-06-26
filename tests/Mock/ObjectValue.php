<?php
/**
 * Created for djin
 * Datetime: 26.06.2019 19:22
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mock;


class ObjectValue
{

    private $myValue;

    public function __construct($value)
    {
        $this->myValue = $value;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->myValue;
    }

}