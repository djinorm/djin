<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:14
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Model\Link;

class LinkMapper extends ObjectMapper
{

    public function __construct()
    {
        parent::__construct(Link::class, [
            'id' => new IdMapper(),
            'model' => new StringMapper()
        ]);
    }

}