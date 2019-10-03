<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 17:37
 */

namespace DjinORM\Djin\Id;


use DjinORM\Djin\Model\ModelInterface;

class MemoryIdGenerator implements IdGeneratorInterface
{

    private $ids = [];
    private $startFrom;

    public function __construct($startFrom = 1)
    {
        $this->startFrom = $startFrom;
    }

    public function getNextId(ModelInterface $model): string
    {
        if (!isset($this->ids[$model::getModelName()])) {
            $this->ids[$model::getModelName()] = $this->startFrom;
        }
        return $this->ids[$model::getModelName()]++;
    }
}