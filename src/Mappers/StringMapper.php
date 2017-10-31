<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;

class StringMapper extends ScalarMapper
{
    /**
     * @var int|null
     */
    private $maxLength;

    public function __construct($modelProperty, $dbColumn = null, $allowNull = false, $maxLength = null)
    {
        parent::__construct($modelProperty, $dbColumn, $allowNull);
        $this->maxLength = $maxLength;
    }

    /**
     * @param array $data
     * @param object $object
     * @return string
     * @throws HydratorException
     */
    public function hydrate(array $data, $object): ?string
    {
        $column = $this->getDbColumn();
        if (!isset($data[$column])) {
            if ($this->isAllowNull()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('string', $object);
        }

        $value = (string) $data[$column];
        RepoHelper::setProperty($object, $this->getModelProperty(), $value);
        return $value;
    }

    /**
     * @param $object
     * @return array
     * @throws ExtractorException
     */
    public function extract($object): array
    {
        /** @var int $value */
        $value = RepoHelper::getProperty($object, $this->getModelProperty());

        if ($value === null && $this->isAllowNull() == false) {
            throw $this->nullExtractorException('string', $object);
        }

        if ($this->maxLength && mb_strlen($value) > $this->maxLength) {
            throw new ExtractorException('String value of ' . $this->getDescription($object) . ' is too much. Max is: ' . $this->maxLength);
        }

        return [
            $this->getDbColumn() => (string) $value
        ];
    }

    public function getFixtures(): array
    {
        $fixtures = [
            '0V1D2OJxaviY8rzGS0RK',
            'KqS8Gr19sEeA87WgQ01D',
            'ltIkmQxtW7fgsFiywArY',
            'lFXhUAyyi5gAqkl5FSm8',
            'zg3f5dDH78O6QA1oTc1n',
            'xLlvPL7DKhi62CfkQwIp',
            '9egIDzBp69woT1GBUY7U',
            'SSevqiSgFY0dNA6wdaph',
        ];

        if ($this->maxLength && $this->maxLength < 20) {
            $fixtures = array_map(function ($value) {
                return substr($value, 0, $this->maxLength);
            }, $fixtures);
        }

        if ($this->isAllowNull()) {
            $fixtures[] = null;
        }

        return $fixtures;
    }

}