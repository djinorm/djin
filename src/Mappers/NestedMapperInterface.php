<?php
/**
 * Created for DjinORM.
 * Datetime: 18.07.2018 18:03
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

interface NestedMapperInterface extends MapperInterface
{

    public function getNestedMappersHandler(): MappersHandlerInterface;

}