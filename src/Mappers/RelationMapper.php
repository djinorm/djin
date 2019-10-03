<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:14
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Model\Relation;

class RelationMapper extends ObjectMapper
{

    public function __construct()
    {
        parent::__construct(Relation::class, [
            'id' => new IdMapper(),
            'model' => new StringMapper()
        ]);
    }

}