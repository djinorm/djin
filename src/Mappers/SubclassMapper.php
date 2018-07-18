<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

class SubclassMapper extends AbstractMapper
{

    /**
     * @var MappersHandlerInterface
     */
    protected $nestedMapper;
    /**
     * @var bool
     */
    private $asJsonString;

    public function __construct(string $modelProperty, string $dbAlias, bool $asJsonString = false, MappersHandlerInterface $nestedMapper)
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias;
        $this->nestedMapper = $nestedMapper;
        $this->asJsonString = $asJsonString;
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
        if ($this->asJsonString) {
            $data = json_decode($data, true);
        }
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
        if ($this->asJsonString) {
            $data = json_encode($data);
        }
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