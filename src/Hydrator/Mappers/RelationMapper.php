<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:14
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;


use DjinORM\Djin\Model\Relation;

class RelationMapper extends NestedMapper
{

    /**
     * ModelPointerMapper constructor.
     * @param string $property
     * @param bool $allowNull
     */
    public function __construct(string $property, bool $allowNull = false)
    {
        parent::__construct($property,  Relation::class, [
            new IdMapper('id'),
            new StringMapper('model')
        ], $allowNull);
    }

}