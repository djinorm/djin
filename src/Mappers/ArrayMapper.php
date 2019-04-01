<?php
/**
 * Created for DjinORM.
 * Datetime: 10.11.2017 15:17
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;

class ArrayMapper extends AbstractMapper implements ArrayMapperInterface
{

    /**
     * @var bool
     */
    protected $allowNullNested;

    public function __construct(string $property, bool $allowNull = false)
    {
        $this->property = $property;
        $this->allowNull = $allowNull;
    }

    /**
     * @param array $data
     * @param object $object
     * @return array|null
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?array
    {
        $property = $this->getProperty();

        if (!isset($data[$property]) || $data[$property] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('array', $object);
        }

        $array = $data[$property];

        RepoHelper::setProperty($object, $this->getProperty(), $array);
        return $array;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        $array = RepoHelper::getProperty($object, $this->getProperty());

        if (!is_array($array)) {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('array', $object);
            }
            return [
                $this->getProperty() => null
            ];
        }

        return [
            $this->getProperty() => $array
        ];
    }
}