<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 19:48
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Model\ModelInterface;

class DjinHelper
{

    /**
     * @param ModelInterface[] $models
     * @return ModelInterface[]
     */
    public static function indexModelsArrayById(array $models): array
    {
        $indexedModels = [];
        foreach ($models as $model) {
            $indexedModels[$model->getId()->toString()] = $model;
        }
        return $indexedModels;
    }

    /**
     * @param ModelInterface[] $models
     * @param callable $callable
     * @return ModelInterface[]
     */
    public static function indexModelsArrayCallback(array $models, callable $callable): array
    {
        $indexedModels = [];
        foreach ($models as $model) {
            $key = $callable($model);
            $indexedModels[$key] = $model;
        }
        return $indexedModels;
    }

}