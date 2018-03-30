<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 19:43
 */

namespace DjinORM\Djin\Exceptions;



class MismatchModelException extends \Exception implements DjinExceptionInterface
{

    public function __construct(string $expected, string $actual, $code = 0)
    {
        $message = "Expected class {$expected}  not equals to {$actual}";
        parent::__construct($message, $code);
    }

}