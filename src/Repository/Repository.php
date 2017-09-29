<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 17:43
 */

namespace DjinORM\Djin\Repository;


use DjinORM\Djin\Exceptions\NotFoundException;
use DjinORM\Djin\Model\ModelInterface;

abstract class Repository implements RepositoryInterface
{

    protected $rawData = [];
    protected $models = [];
    protected $queryCount = 0;

    public function save(ModelInterface $model)
    {
        if ($this->isNew($model)) {
            return $this->insert($model);
        } else {
            return $this->update($model);
        }
    }

    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    /**
     * @param int|string $id
     * @param \Exception|null $exception
     * @return ModelInterface|null
     * @throws \Exception
     */
    public function findByIdOrException($id, \Exception $exception = null)
    {
        $model = $this->findById($id);
        if ($model === null) {
            if (!is_null($exception)) {
                throw $exception;
            } else {
                throw new NotFoundException("Model with ID '{$id}' was not found");
            }
        }
        return $model;
    }

    protected function incQueryCount(): int
    {
        return $this->queryCount++;
    }

    /**
     * Возвращает массив измененных данных для обновления модели. Т.е. удаляет
     * те поля, которые не изменились
     *
     * @param ModelInterface $model
     * @return array
     */
    protected function getDiffDataForUpdate(ModelInterface $model): array
    {
        $data = $this->extract($model);
        $data = array_diff_assoc($data, $this->rawData[$model->getId()->toScalar()]);
        return $data;
    }

    /**
     * @param int|string $id
     * @return ModelInterface|bool
     */
    protected function loadedById($id)
    {
        if (isset($this->models[$id])) {
            return $this->models[$id];
        }

        return false;
    }

    /**
     * @param int[]|string[] $ids
     * @return ModelInterface[]
     */
    protected function loadedByIds(array $ids):array
    {
        $models = [];
        foreach ($ids as $id) {
            if (isset($this->models[$id])) {
                $models[$id] = $this->models[$id];
            }
        }
        return $models;
    }

    abstract protected static function getModelClass(): string;

    /**
     * Превращает массив в объект нужного класса
     * @param array $data
     * @return ModelInterface
     */
    abstract protected function hydrate(array $data): ModelInterface;

    /**
     * @param ModelInterface $object
     * @return array
     */
    abstract protected function extract(ModelInterface $object): array;

    protected function populate($data): ?ModelInterface
    {
        if ($data === null) return null;

        /** @var ModelInterface $className */
        $className = static::getModelClass();
        $id = $data[$className::getModelIdPropertyName()];

        if (!isset($this->models[$id])) {
            $this->rawData[$id] = $data;
            $this->models[$id] = $this->hydrate($data);
        }

        return $this->models[$id];
    }

    /**
     * @param $dataArray
     * @return ModelInterface[]
     */
    protected function populateArray($dataArray): array
    {
        $models = [];
        foreach ($dataArray as $data) {
            $models[] = $this->populate($data);
        }
        return $models;
    }

    protected function isNew(ModelInterface $model)
    {
        return !isset($this->models[$model->getId()->toScalar()]);
    }

}