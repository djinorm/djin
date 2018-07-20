<?php
/**
 * Created for DjinORM.
 * Datetime: 20.07.2018 12:36
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

interface ArrayMapperInterface extends MapperInterface
{

    public function getNestedMappersHandler(): ?MappersHandlerInterface;

}