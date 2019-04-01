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

    public function __construct($property, $allowNull = false, $isImmutable = true, $format = 'Y-m-d H:i:s')
    {
        $this->property = $property;
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
    public function hydrate(array $data, object $object): ?DateTimeInterface
    {
        $property = $this->getProperty();

        if (!isset($data[$property]) || $data[$property] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException($this->getClassName(), $object);
        }

        $class = $this->getClassName();
        $datetime = new $class($data[$property]);
        RepoHelper::setProperty($object, $this->getProperty(), $datetime);
        return $datetime;
    }

    /**
     * @param object $object
     * @return array
     * @throws \DjinORM\Djin\Exceptions\ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        /** @var DateTimeInterface $datetime */
        $datetime = RepoHelper::getProperty($object, $this->getProperty());

        if ($datetime === null || $datetime === '') {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException($this->getClassName(), $object);
            }
            return [
                $this->getProperty() => null
            ];
        }

        return [
            $this->getProperty() => $datetime->format($this->format)
        ];
    }

    protected function getClassName(): string
    {
        return $this->isImmutable ? \DateTimeImmutable::class : \DateTime::class;
    }
}