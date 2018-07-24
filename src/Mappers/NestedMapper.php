<?php
/**
 * Created for DjinORM.
 * Datetime: 15.02.2018 11:25
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Helpers\RepoHelper;
use DjinORM\Djin\Mappers\Handler\MappersHandlerInterface;

class NestedMapper extends AbstractMapper implements NestedMapperInterface
{

    /**
     * @var MappersHandlerInterface
     */
    protected $nestedMapper;

    public function __construct(string $modelProperty, string $dbAlias = null, MappersHandlerInterface $nestedMapper, bool $allowNull = false)
    {
        $this->modelProperty = $modelProperty;
        $this->dbAlias = $dbAlias ?? $modelProperty;
        $this->nestedMapper = $nestedMapper;
        $this->allowNull = $allowNull;
    }

    /**
     * @param array $data
     * @param object $object
     * @return mixed
     * @throws \DjinORM\Djin\Exceptions\HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $object)
    {
        if (!isset($data[$this->getDbAlias()])) {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException($this->nestedMapper->getModelClassName(), $object);
        }

        $data = $data[$this->getDbAlias()];
        $subObject = $this->nestedMapper->hydrate($data);
        RepoHelper::setProperty($object, $this->getModelProperty(), $subObject);
        return $subObject;
    }

    /**
     * @param $object
     * @return array
     * @throws \DjinORM\Djin\Exceptions\ExtractorException
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        $subObject = RepoHelper::getProperty($object, $this->modelProperty);

        if ($subObject === null) {
            if ($this->isNullAllowed() == true) {
                return [$this->getDbAlias() => null];
            }
            throw $this->nullExtractorException($this->nestedMapper->getModelClassName(), $object);
        }

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