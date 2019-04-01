<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:14
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Model\ModelPointer;

class ModelPointerMapper extends NestedMapper
{

    /**
     * ModelPointerMapper constructor.
     * @param string $property
     * @param bool $allowNull
     */
    public function __construct(string $property, bool $allowNull = false)
    {
        parent::__construct($property,  ModelPointer::class, [
            new IdMapper('id'),
            new StringMapper('model')
        ], $allowNull);
    }

}