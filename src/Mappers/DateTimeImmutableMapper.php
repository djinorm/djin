<?php
/**
 * Created for DjinORM.
 * Datetime: 31.10.2017 12:03
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DateTimeImmutable;

class DateTimeImmutableMapper extends DateTimeMapper
{

    /**
     * @return string
     */
    protected function classname(): string
    {
        return DateTimeImmutable::class;
    }

}