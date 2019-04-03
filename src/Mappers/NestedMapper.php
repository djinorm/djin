<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\Handler\MappersHandler;

class NestedMapper extends AbstractMapper implements NestedMapperInterface
{

    /**
     * @var MappersHandler
     */
    protected $nestedMapper;

    public function __construct(
        string $property,
        string $classname,
        array $mappers,
        bool $allowNull = false
    )
    {
        $this->property = $property;
        $this->nestedMapper = new MappersHandler($classname, $mappers);
        $this->allowNull = $allowNull;
    }

    /**
     * @param array $data
     * @param object $object
     * @return mixed
     * @throws \DjinORM\Djin\Exceptions\HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object)
    {
        if (!isset($data[$this->getProperty()])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException($this->nestedMapper->getModelClassName(), $object);
        }

        $data = $data[$this->getProperty()];
        $subObject = $this->nestedMapper->hydrate($data);
        RepoHelper::setProperty($object, $this->getProperty(), $subObject);
        return $subObject;
    }

    /**
     * @param object $object
     * @return array
     * @throws \DjinORM\Djin\Exceptions\ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        $subObject = RepoHelper::getProperty($object, $this->property);

        if ($subObject === null) {
            if ($this->isNullAllowed() == true) {
                return [$this->getProperty() => null];
            }
            throw $this->nullExtractorException($this->nestedMapper->getModelClassName(), $object);
        }

        $data = $this->nestedMapper->extract($subObject);
        return [
            $this->getProperty() => $data,
        ];
    }

    /**
     * @return MappersHandler
     */
    public function getNestedMappersHandler(): MappersHandler
    {
        return $this->nestedMapper;
    }
}