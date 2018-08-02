<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:14
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Model\Shadow;

class ShadowMapper extends NestedMapper
{

    public function __construct(string $modelProperty, string $dbAlias = null, bool $allowNull = false)
    {
        parent::__construct($modelProperty, $dbAlias, Shadow::class, [
            new IdMapper('id'),
            new StringMapper('model')
        ], $allowNull);
    }

}