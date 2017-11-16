<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 07.07.2017 1:56
 */

namespace DjinORM\Djin\Exceptions;


class NotFoundException extends \Exception implements DjinExceptionInterface
{

    public function __construct($message = "", $code = 404)
    {
        parent::__construct($message, $code);
    }

}