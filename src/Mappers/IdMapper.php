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

    public function __construct(bool $asInteger = true)
    {
        $mapper = $asInteger ? new IntMapper() : new StringMapper();
        parent::__construct(Id::class, $mapper, 'permanentId');
    }

}