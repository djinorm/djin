<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Id\Id;

class IdMapper extends ValueObjectMapper
{

    public function __construct()
    {
        parent::__construct(
            Id::class,
            new StringMapper(),
            'permanentId'
        );
    }

}