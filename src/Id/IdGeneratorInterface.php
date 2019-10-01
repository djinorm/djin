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
     * Важное замечание: метод НЕ ДОЛЖЕН проставлять Id для модели
     * @param ModelInterface $model - модель, которой нужно проставить Id
     * @return string должен вернуть СТРОКУ Id
     */
    public function getNextId(ModelInterface $model): string;

}