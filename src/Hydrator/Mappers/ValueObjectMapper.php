<?php
/**
 * Created for DjinORM
 * Datetime: 26.06.2019 19:00
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Hydrator\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;
use ReflectionException;

class ValueObjectMapper extends ScalarMapper
{

    /**
     * @var string
     */
    private $classname;
    /**
     * @var MapperInterface
     */
    private $mapper;

    public function __construct(string $property, string $classname, MapperInterface $mapper)
    {
        parent::__construct($property, $mapper->isNullAllowed());
        $this->classname = $classname;
        $this->mapper = $mapper;
    }

    /**
     * Превращает простой массив в объект нужного типа
     * @param array $data
     * @param object $object
     * @return mixed
     * @throws HydratorException
     * @throws ReflectionException
     */
    public function hydrate(array $data, object $object)
    {
        if (!isset($data[$this->getProperty()])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException($this->classname, $object);
        }

        $valueObject = RepoHelper::newWithoutConstructor($this->classname);
        $rawValue = $data[$this->getProperty()];
        $this->mapper->hydrate([$this->mapper->getProperty() => $rawValue], $valueObject);

        RepoHelper::setProperty($object, $this->getProperty(), $valueObject);
        return $valueObject;
    }

    /**
     * Превращает объект в простой массив
     * @param object $object
     * @return array
     * @throws ReflectionException
     * @throws ExtractorException
     */
    public function extract(object $object): array
    {
        $valueObject = RepoHelper::getProperty($object, $this->property);

        if ($valueObject === null) {
            if ($this->isNullAllowed() == true) {
                return [$this->getProperty() => null];
            }
            throw $this->nullExtractorException($this->classname, $object);
        }

        $data = $this->mapper->extract($valueObject);
        return [
            $this->getProperty() => $data[$this->mapper->getProperty()],
        ];
    }
}