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
     * @return int|string должен вернуть скалярное представление Id
     */
    public function getNextId(ModelInterface $model);

}