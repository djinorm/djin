<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;


abstract class ScalarMapper extends AbstractMapper
{

    /**
     * ScalarMapper constructor.
     * @param string $property
     * @param bool $allowNull
     */
    public function __construct(string $property, bool $allowNull = false)
    {
        $this->property = $property;
        $this->allowNull = $allowNull;
    }

}