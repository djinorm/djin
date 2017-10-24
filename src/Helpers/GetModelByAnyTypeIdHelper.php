<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 20:07
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\RepositoryInterface;

class GetModelByAnyTypeIdHelper
{

    /**
     * @param $modelObjectOrAnyId ModelInterface|Id|int|string
     * @param RepositoryInterface|null $repo
     * @param \Exception|null $exception
     * @return ModelInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public static function get(
        $modelObjectOrAnyId,
        RepositoryInterface $repo = null,
        \Exception $exception = null
    ): ModelInterface
    {

        if ($modelObjectOrAnyId instanceof ModelInterface) {
            return $modelObjectOrAnyId;
        }

        if (is_null($repo)) {
            throw new LogicException('Impossible to find model without repository');
        }

        if (is_scalar($modelObjectOrAnyId)) {
            return $repo->findByIdOrException($modelObjectOrAnyId, $exception);
        }

        if ($modelObjectOrAnyId instanceof Id) {
            /** @var $modelObjectOrAnyId Id */
            return $repo->findByIdOrException($modelObjectOrAnyId->toScalar(), $exception);
        }

        throw new InvalidArgumentException('Incorrect ID type');
    }

}