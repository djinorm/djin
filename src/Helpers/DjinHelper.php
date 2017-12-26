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
     * @param \Exception|null $exception
     * @return ModelInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public static function getModelByAnyTypeIdArgument(
        $modelObjectOrAnyId,
        RepositoryInterface $repo = null,
        \Exception $exception = null
    ): ModelInterface
    {
        return GetModelByAnyTypeIdHelper::get($modelObjectOrAnyId, $repo, $exception);
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
     * @param $modelOrId
     * @param string|null $checkThatModelClassIs
     * @return int|null|string
     * @throws MismatchModelException
     * @throws NotPermanentIdException
     */
    public static function getScalarIdOrNull($modelOrId, string $checkThatModelClassIs = null)
    {
        try {
            return self::getScalarId($modelOrId, $checkThatModelClassIs);
        } catch (InvalidArgumentException $exception) {
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

}