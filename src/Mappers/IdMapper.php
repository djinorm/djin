<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\SerializerException;
use DjinORM\Djin\Id\Id;

class IdMapper extends CallableMapper
{

    public function __construct()
    {
        parent::__construct(
            function (Id $id) {
                return $id->toString();
            },
            function ($id) {
                if (!is_string($id) && !is_int($id) && !is_float($id)) {
                    throw new SerializerException("Id expected, but '" . gettype($id) . "' passed");
                }
                return new Id($id);
            }
        );
    }

}