<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:05
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Model;


use DjinORM\Djin\Id\Id;

class ModelPointer
{

    /** @var Id */
    protected $id;

    /** @var string */
    protected $model;

    /**
     * ModelPointer constructor.
     * @param ModelInterface|string $modelOrName
     * @param Id|int|string $id
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function __construct($modelOrName, $id = null)
    {
        if ($modelOrName instanceof ModelInterface) {
            $this->model = $modelOrName::getModelName();
            $this->id = $modelOrName->getId();
        } else {
            if ($id instanceof Id) {
                $this->id = $id;
            } else {
                $this->id = new Id($id);
            }

            $this->model = $modelOrName;
        }
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

}