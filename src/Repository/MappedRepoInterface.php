<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 03.10.2019 22:53
 */

namespace DjinORM\Djin\Repository;


use DjinORM\Djin\Mappers\MapperInterface;

interface MappedRepoInterface extends RepoInterface
{

    public function getMapper(): MapperInterface;

}