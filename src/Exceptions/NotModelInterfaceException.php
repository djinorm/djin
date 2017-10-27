<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 05.04.2017 2:37
 */

namespace DjinORM\Djin\Exceptions;


class NotModelInterfaceException extends \Exception implements DjinExceptionInterface
{

    public function __construct()
    {
        parent::__construct('Object should be instance of ModelInterface', 0);
    }

}