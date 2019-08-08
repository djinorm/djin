<?php
/**
 * Created for DjinORM.
 * Datetime: 18.07.2018 18:03
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;


use DjinORM\Djin\Hydrator\Hydrator;

interface NestedMapperInterface extends MapperInterface
{

    public function getNestedMappersHandler(): Hydrator;

}