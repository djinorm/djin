<?php
/**
 * Created for DjinORM.
 * Datetime: 05.02.2018 15:11
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\MismatchModelException;
use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Model\ModelInterface;

class Mapper implements MapperInterface
{
    /**
     * @var string
     */
    private $classname;
    /**
     * @var MapperInterface[]
     */
    private $mappers;

    /**
     * Mapper constructor.
     * @param string $classname
     * @param array $mappers
     */
    public function __construct(string $classname, array $mappers)
    {
        $this->classname = $classname;
        $this->mappers = $mappers;
    }

    /**
     * @param array $data
     * @param object $object *
     * @return mixed
     * @throws MismatchModelException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object = null)
    {
        if ($object === null) {
            /** @var ModelInterface $model */
            $object = RepoHelper::newWithoutConstructor($this->classname);
        } else {
            $this->guardInvalidModel($object);
        }

        foreach ($this->mappers as $mapper) {
            $mapper->hydrate($data, $object);
        }

        return $object;
    }

    public function extract($object): array
    {
        $data = [];
        foreach ($this->mappers as $mapper) {
            $data = array_merge($data, $mapper->extract($object));
        }
        return $data;
    }

    /**
     * @param $object
     * @throws MismatchModelException
     */
    private function guardInvalidModel($object)
    {
        if (!is_a($object, $this->classname)) {
            throw new MismatchModelException("{$this->classname}  not equals to " . get_class($object));
        }
    }
}