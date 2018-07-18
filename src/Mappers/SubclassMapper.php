<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;
use DjinORM\Djin\Mappers\Notations\NotationInterface;

class SubclassMapper extends AbstractMapper
{

    /**
     * @var MappersHandlerInterface
     */
    protected $nestedMapper;
    /**
     * @var NotationInterface
     */
    private $notation;

    public function __construct(string $modelProperty, string $dbAlias, NotationInterface $notation, MappersHandlerInterface $nestedMapper)
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias;
        $this->nestedMapper = $nestedMapper;
        $this->notation = $notation;
    }

    /**
     * @param array $data
     * @param object $object
     * @return mixed
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object)
    {
        $data = $data[$this->getDbAlias()];
        $data = $this->notation->decode($data);
        $subObject = $this->nestedMapper->hydrate($data);
        RepoHelper::setProperty($object, $this->modelProperty, $subObject);
        return $subObject;
    }

    /**
     * @param $object
     * @return array
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        $subObject = RepoHelper::getProperty($object, $this->modelProperty);
        $data = $this->nestedMapper->extract($subObject);
        $data = $this->notation->encode($data);
        return [
            $this->getDbAlias() => $data,
        ];
    }

    /**
     * @return MappersHandlerInterface
     */
    public function getNestedMappersHandler(): MappersHandlerInterface
    {
        return $this->nestedMapper;
    }
}