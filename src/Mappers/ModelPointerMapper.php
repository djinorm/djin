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
     * @param string $modelProperty
     * @param bool $allowNull
     * @param string|null $dbAlias
     */
    public function __construct(string $modelProperty, bool $allowNull = false, string $dbAlias = null)
    {
        parent::__construct($modelProperty,  ModelPointer::class, [
            new IdMapper('id'),
            new StringMapper('model')
        ], $allowNull, $dbAlias);
    }

}