<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 20:07
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Exceptions\NotFoundException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\RepoInterface;

class GetModelByAnyTypeIdHelper
{

    /**
     * @param $modelObjectOrAnyId ModelInterface|Id|int|string
     * @param RepoInterface|null $repo
     * @return ModelInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws NotFoundException
     */
    public static function get(
        $modelObjectOrAnyId,
        RepoInterface $repo = null
    ): ModelInterface
    {

        if ($modelObjectOrAnyId instanceof ModelInterface) {
            return $modelObjectOrAnyId;
        }

        if (is_null($repo)) {
            throw new LogicException('Impossible to find model without repository');
        }


        if (is_scalar($modelObjectOrAnyId)) {
            $model = $repo->findById($modelObjectOrAnyId);
            if ($model === null) {
                self::throwNotFoundException($modelObjectOrAnyId);
            }
            return $model;
        }

        if ($modelObjectOrAnyId instanceof Id) {
            $id = $modelObjectOrAnyId->toString();
            $model = $repo->findById($id);
            if ($model === null) {
                self::throwNotFoundException($id);
            }
            return $model;
        }

        throw new InvalidArgumentException('Incorrect ID type');
    }

    /**
     * @param $modelId
     * @throws NotFoundException
     */
    private static function throwNotFoundException($modelId)
    {
        throw new NotFoundException("Model with ID '{$modelId}' was not found");
    }

}