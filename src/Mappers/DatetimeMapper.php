<?php
/**
 * Created for DjinORM.
 * Datetime: 31.10.2017 12:03
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DateTimeInterface;
use DjinORM\Djin\Helpers\RepoHelper;

class DatetimeMapper extends AbstractMapper
{

    /**
     * @var bool
     */
    private $isImmutable;
    /**
     * @var string
     */
    private $format;

    public function __construct($modelProperty, $dbAlias = null, $allowNull = false, $isImmutable = true, $format = 'Y-m-d H:i:s')
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias ?? $modelProperty;
        $this->allowNull = $allowNull;
        $this->isImmutable = $isImmutable;
        $this->format = $format;
    }

    /**
     * @param array $data
     * @param object $object
     * @return DateTimeInterface|null
     * @throws \DjinORM\Djin\Exceptions\HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object): ?DateTimeInterface
    {
        $column = $this->getDbAlias();

        if (!isset($data[$column]) || $data[$column] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException($this->getClassName(), $object);
        }

        $class = $this->getClassName();
        $datetime = new $class($data[$column]);
        RepoHelper::setProperty($object, $this->getModelProperty(), $datetime);
        return $datetime;
    }

    /**
     * @param $object
     * @return array
     * @throws \DjinORM\Djin\Exceptions\ExtractorException
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        /** @var DateTimeInterface $datetime */
        $datetime = RepoHelper::getProperty($object, $this->getModelProperty());

        if ($datetime === null || $datetime === '') {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException($this->getClassName(), $object);
            }
            return [
                $this->getDbAlias() => null
            ];
        }

        return [
            $this->getDbAlias() => $datetime->format($this->format)
        ];
    }

    protected function getClassName(): string
    {
        return $this->isImmutable ? \DateTimeImmutable::class : \DateTime::class;
    }
}