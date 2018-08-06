<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;



abstract class ScalarMapper extends AbstractMapper implements ScalarMapperInterface
{

    /**
     * ScalarMapper constructor.
     * @param string $modelProperty
     * @param bool $allowNull
     * @param string|null $dbAlias
     */
    public function __construct(string $modelProperty, bool $allowNull = false, string $dbAlias = null)
    {
        $this->modelProperty = $modelProperty;
        $this->allowNull = $allowNull;
        $this->dbAlias = $dbAlias ?? $modelProperty;
    }

}