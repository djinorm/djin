<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 11:28
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;

class BoolMapper extends ScalarMapper
{

    public function __construct($modelProperty, $dbAlias = null)
    {
        parent::__construct($modelProperty, $dbAlias, true);
    }

    /**
     * @param array $data
     * @param object $object
     * @return bool
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object): bool
    {
        $column = $this->getDbAlias();
        $value = $data[$column] ?? false;

        if (mb_strtolower($value) === 'false') {
            $value = false;
        } else {
            $value = (bool) $data[$column];
        }

        RepoHelper::setProperty($object, $this->getModelProperty(), $value);
        return $value;
    }

    /**
     * @param $object
     * @return array
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        /** @var bool $value */
        $value = RepoHelper::getProperty($object, $this->getModelProperty());

        if (mb_strtolower($value) === 'false') {
            $value = false;
        } else {
            $value = (bool) $value;
        }

        return [
            $this->getDbAlias() => (int) $value
        ];
    }

}
