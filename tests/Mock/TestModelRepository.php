<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 18:44
 */

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\Repository;

class TestModelRepository extends Repository
{

    private $repository = [
        1 => ['id' => 1, 'otherId' => null],
        2 => ['id' => 2, 'otherId' => 1],
    ];

    /**
     * Превращает массив в объект нужного класса
     * @param array $data
     * @return ModelInterface
     */
    protected function hydrate(array $data): ModelInterface
    {
        /** @var TestModel $className */
        $className = self::getModelClass();
        return new $className($data['id'], $data['otherId']);
    }

    /**
     * @param ModelInterface|TestModel $object
     * @return array
     */
    protected function extract(ModelInterface $object): array
    {
        return [
            $object->getId()->toScalar(),
            $object->getOtherId()->toScalar()
        ];
    }

    /**
     * @param $id
     * @return ModelInterface|null
     */
    public function findById($id)
    {
        if ($model = $this->loadedById($id)) {
            return $model;
        }
        $this->incQueryCount();
        return $this->populate($this->repository[$id] ?? null);
    }

    /**
     * @param array $ids
     * @return ModelInterface[]
     */
    public function findByIds(array $ids): array
    {
        $result = $this->loadedByIds($ids);
        if (count($ids) == count($result)) {
            return $result;
        }

        $result = [];
        $this->incQueryCount();
        foreach ($ids as $id) {
            if (isset($this->repository[$id])) {
                $result[$id] = $this->populate($this->repository[$id]);
            }
        }
        return $result;
    }

    public function insert(ModelInterface $model)
    {
        $this->incQueryCount();
        $this->repository[$model->getId()->toScalar()] = $this->extract($model);
    }

    public function update(ModelInterface $model)
    {
        $this->queryCount++;
        $data = $this->getDiffDataForUpdate($model);
        $this->repository[$model->getId()->toScalar()] = $data;
    }

    public function delete(ModelInterface $model)
    {
        $this->incQueryCount();
        unset($this->repository[$model->getId()->toScalar()]);
    }

    /**
     * Сообщает, может ли репозиторий откатить изменения. Если да, то
     * @see ModelManager сохранит эту модель одной из первых
     * @return bool
     */
    public function isTransactional(): bool
    {
        return false;
    }

    public static function getModelClass(): string
    {
        return TestModel::class;
    }
}