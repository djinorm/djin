<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:05
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Model;


use DjinORM\Djin\Id\Id;

class Shadow
{

    /** @var Id */
    protected $id;

    /** @var string */
    protected $model;

    public function __construct(Id $id, string $name)
    {
        $this->id = $id;
        $this->model = $name;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->model;
    }

    /**
     * @param ModelInterface $model
     * @return Shadow
     */
    public static function createFromModel(ModelInterface $model): self
    {
        return new self($model->getId(), $model::getModelName());
    }

}