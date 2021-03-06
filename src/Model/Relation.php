<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:05
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Model;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Id\Id;
use JsonSerializable;

class Relation implements JsonSerializable
{

    /** @var Id */
    protected $id;

    /** @var string */
    protected $model;

    /**
     * ModelPointer constructor.
     * @param string $modelName
     * @param Id|int|string $id
     */
    public function __construct(string $modelName, Id $id)
    {
        $this->model = $modelName;
        $this->id = $id;
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
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'model' => $this->model,
            'id' => $this->id->toScalar(),
        ];
    }

    public static function link(ModelInterface $model): self
    {
        return new static($model::getModelName(), $model->getId());
    }

    /**
     * @param string $json
     * @return Relation|null
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public static function fromJson(string $json): ?self
    {
        $data = json_decode($json, true);
        if (is_array($data) && isset($data['model']) && isset($data['id'])) {
            return new static($data['model'], new Id($data['id']));
        }
        return null;
    }
}