<?php


namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\Link;
use DjinORM\Djin\Model\ModelInterface;

class IdHelper
{

    /**
     * @param ModelInterface|Link|Id|string|int $modelOrId
     * @return string|null
     */
    public static function scalarizeOne($modelOrId): ?string
    {
        if ($modelOrId instanceof ModelInterface) {
            return (string) $modelOrId->getId();
        }

        if ($modelOrId instanceof Link) {
            return (string) $modelOrId->getId();
        }

        if ($modelOrId instanceof Id) {
            return (string) $modelOrId;
        }

        return $modelOrId;
    }

    /**
     * @param ModelInterface[]|Link[]|Id[]|string[]|int[] $modelsOrIds
     * @return string[]|null[]
     */
    public static function scalarizeMany(array $modelsOrIds): array
    {
        $result = [];
        foreach ($modelsOrIds as $modelOrId) {
            $result[] = static::scalarizeOne($modelOrId);
        }
        return $result;
    }

}