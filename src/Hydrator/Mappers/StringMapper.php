<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Hydrator\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;

class StringMapper extends ScalarMapper
{
    /**
     * @var int|null
     */
    private $maxLength;

    public function __construct($property, $maxLength = null, $allowNull = false)
    {
        parent::__construct($property, $allowNull);
        $this->maxLength = $maxLength;
    }

    /**
     * @param array $data
     * @param object $object
     * @return string
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?string
    {
        $property = $this->getProperty();
        if (!isset($data[$property])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('string', $object);
        }

        $value = (string) $data[$property];
        RepoHelper::setProperty($object, $this->getProperty(), $value);
        return $value;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        /** @var int $value */
        $value = RepoHelper::getProperty($object, $this->getProperty());

        if ($value === null && $this->isNullAllowed() == false) {
            throw $this->nullExtractorException('string', $object);
        }

        if ($this->maxLength && mb_strlen($value) > $this->maxLength) {
            throw new ExtractorException('String value of ' . $this->getDescription($object) . ' is too much. Max is: ' . $this->maxLength);
        }

        return [
            $this->getProperty() => (string) $value
        ];
    }

}