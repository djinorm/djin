<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 18:41
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers\Notations;


class DotNotation extends CustomCharNotation
{

    public function __construct()
    {
        parent::__construct('.');
    }

}