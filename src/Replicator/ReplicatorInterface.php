<?php
/**
 * Created for djin
 * Date: 08.03.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DjinORM\Djin\Replicator;


use DjinORM\Djin\Manager\Commit;

interface ReplicatorInterface
{

    /**
     * @param Commit $commit
     */
    public function commit(Commit $commit): void;

}