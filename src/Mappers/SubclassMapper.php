<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

class SubclassMapper extends AbstractMapper implements NestedMapperInterface
{

    /**
     * @var MappersHandlerInterface
     */
    protected $nestedMapper;

    public function __construct(string $modelProperty, string $dbAlias, MappersHandlerInterface $nestedMapper)
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias;
        $this->nestedMapper = $nestedMapper;
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