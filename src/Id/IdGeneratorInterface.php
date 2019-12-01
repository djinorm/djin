<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 16:25
 */

namespace DjinORM\Djin\Id;

use DjinORM\Djin\Model\ModelInterface;

interface IdGeneratorInterface
{

    /**
     * @param ModelInterface $model
     * @return Id
     */
    public function __invoke(ModelInterface $model): Id;

}