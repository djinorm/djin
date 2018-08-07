<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 19:48
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Exceptions\MismatchModelException;
use DjinORM\Djin\Exceptions\NotPermanentIdException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\RepositoryInterface;

class DjinHelper
{

    /**
     * @param $modelObjectOrAnyId ModelInterface|Id|int|string
     * @param RepositoryInterface|null $repo
     * @return ModelInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws \DjinORM\Djin\Exceptions\NotFoundException
     */
    public static function getModelByAnyTypeIdArgument(
        $modelObjectOrAnyId,
        RepositoryInterface $repo = null
    ): ModelInterface
    {
        return GetModelByAnyTypeIdHelper::get($modelObjectOrAnyId, $repo);
    }

    /**
     * @param ModelInterface|Id|int|string $modelOrId
     * @param string|null $checkThatModelClassIs
     * @return int|string
     * @throws MismatchModelException
     * @throws InvalidArgumentException
     * @throws NotPermanentIdException
     */
    public static function getScalarId($modelOrId, string $checkThatModelClassIs = null)
    {
        return GetScalarIdHelper::get($modelOrId, $checkThatModelClassIs);
    }

    /**
     * @param array $modelsOrIds
     * @param string|null $checkThatModelClassIs
     * @return array
     * @throws InvalidArgumentException
     * @throws MismatchModelException
     * @throws NotPermanentIdException
     */
    public static function getScalarIds(array $modelsOrIds, string $checkThatModelClassIs = null): array
    {
        $ids = [];
        foreach ($modelsOrIds as $modelsOrId) {
            $ids[] = self::getScalarId($modelsOrId, $checkThatModelClassIs);
        }
        return $ids;
    }

    /**
     * @param $modelOrId
     * @param string|null $checkThatModelClassIs
     * @return int|null|string
     * @throws MismatchModelException
     */
    public static function getScalarIdOrNull($modelOrId, string $checkThatModelClassIs = null)
    {
        try {
            return self::getScalarId($modelOrId, $checkThatModelClassIs);
        } catch (InvalidArgumentException | NotPermanentIdException $exception) {
            return null;
        }
    }

    /**
     * @param ModelInterface[] $models
     * @return ModelInterface[]
     */
    public static function indexModelsArrayById(array $models): array
    {
        $indexedModels = [];
        foreach ($models as $model) {
            $indexedModels[$model->getId()->toScalar()] = $model;
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