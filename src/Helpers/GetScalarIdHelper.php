<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 27.09.2017 20:11
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\MismatchModelException;
use DjinORM\Djin\Exceptions\NotPermanentIdException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;

class GetScalarIdHelper
{

    /**
     * @param ModelInterface|Id|int|string $modelOrId
     * @param string|null $checkThatModelClassIs
     * @return int|string
     * @throws MismatchModelException
     * @throws InvalidArgumentException
     * @throws NotPermanentIdException
     */
    public static function get($modelOrId, string $checkThatModelClassIs = null)
    {

        if ($modelOrId instanceof ModelInterface) {

            if ($checkThatModelClassIs && get_class($modelOrId) != $checkThatModelClassIs) {
                throw new MismatchModelException($checkThatModelClassIs, get_class($modelOrId));
            }

            if (!$modelOrId->getId()->isPermanent()) {
                throw new NotPermanentIdException(
                    'Id is not permanent'
                );
            }

            return $modelOrId->getId()->toScalar();
        }

        if ($modelOrId instanceof Id) {

            if (!$modelOrId->isPermanent()) {
                throw new NotPermanentIdException(
                    'Id is not permanent'
                );
            }

            return $modelOrId->toScalar();
        }

        if (is_scalar($modelOrId)) {
            return $modelOrId;
        }

        throw new InvalidArgumentException(
            'Incorrect model or id type'
        );
    }

}