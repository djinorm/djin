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
     * Mapper constructor.
     * @param string $modelProperty
     * @param string $dbAlias
     * @param bool $allowNull
     */
    public function __construct(string $modelProperty, string $dbAlias = null, bool $allowNull = false)
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias ?? $modelProperty;
        $this->allowNull = $allowNull;
    }

}