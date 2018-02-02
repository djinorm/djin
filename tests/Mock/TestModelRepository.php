<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 18:44
 */

namespace DjinORM\Djin\Mock;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Id\MemoryIdGenerator;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Repository\RepositoryInterface;

class TestModelRepository implements RepositoryInterface
{

    /** @var int */
    private $clear = 0;

    /** @var MemoryIdGenerator */
    private $idGenerator;

    private $repository = [
        1 => ['id' => 1, 'otherId' => null],
        2 => ['id' => 2, 'otherId' => 1],
    ];

    /**
     * @param $id
     * @return ModelInterface|null
     */
    public function findById($id)
    {
        if (isset($this->repository[$id])) {
            $data = $this->repository[$id];
            return new TestModel($data['id'], $data['otherId']);
        }
        return null;
    }

    /**
     * @param TestModel|ModelInterface $model
     * @return mixed|void
     */
    public function insert(ModelInterface $model)
    {
        $this->setPermanentId($model);
        $this->repository[$model->getId()->toScalar()] = [
            'id' => $model->getId()->getPermanentOrNull(),
            'otherId' => $model->getOtherId()->getPermanentOrNull(),
        ];
    }

    /**
     * @param TestModel|ModelInterface $model
     * @return mixed|void
     */
    public function update(ModelInterface $model)
    {
        $this->repository[$model->getId()->toScalar()] = [
            'id' => $model->getId()->getPermanentOrNull(),
            'otherId' => $model->getOtherId()->getPermanentOrNull(),
        ];
    }

    public function delete(ModelInterface $model)
    {
        unset($this->repository[$model->getId()->toScalar()]);
    }

    private function getIdGenerator(): IdGeneratorInterface
    {
        if ($this->idGenerator === null) {
            $this->idGenerator = new MemoryIdGenerator();
        }
        return $this->idGenerator;
    }

    public function setPermanentId(ModelInterface $model): Id
    {
        if ($model->getId()->isPermanent() === false) {
            $nextId = $this->getIdGenerator()->getNextId($model);
            $model->getId()->setPermanentId($nextId);
        }
        return $model->getId();
    }

    public function save(ModelInterface $model)
    {
        if (isset($this->repository[$model->getId()->toScalar()])) {
            $this->update($model);
        } else {
            $this->insert($model);
        }
    }

    /**
     * Освобождает из памяти загруженные модели.
     * ВНИМАНИЕ: после освобождения памяти в случае сохранения существующей модели через self::save()
     * в БД будет вставлена новая запись вместо обновления существующей
     */
    public function clear()
    {
        return $this->clear++;
    }

    public static function getModelClass(): string
    {
        return TestModel::class;
    }
}