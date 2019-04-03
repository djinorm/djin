<?php
/**
 * Created for DjinORM.
 * Datetime: 17.07.2018 12:39
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Repository;


use DjinORM\Djin\Mappers\Handler\MappersHandler;

interface MappedRepositoryInterface extends RepositoryInterface
{

    public function getMappersHandler(): MappersHandler;

}