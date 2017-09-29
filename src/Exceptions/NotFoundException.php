<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 07.07.2017 1:56
 */

namespace DjinORM\Djin\Exceptions;


class NotFoundException extends \Exception
{

    public function __construct($message = "")
    {
        parent::__construct($message, 404);
    }

}