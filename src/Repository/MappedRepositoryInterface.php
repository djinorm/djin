<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 12:39
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Repository;


use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

interface MappedRepositoryInterface extends RepositoryInterface
{

    public function getMappersHandler(): MappersHandlerInterface;

    public function getAlias(string $modelProperty): string;

}