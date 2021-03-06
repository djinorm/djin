<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 13:45
 */

namespace DjinORM\Djin\Id;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Model\ModelInterface;
use JsonSerializable;

class Id implements JsonSerializable
{

    private $permanentId;

    /**
     * Id constructor.
     * @param null $permanentId
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function __construct($permanentId = null)
    {
        if (is_null($permanentId) === false) {
            $this->setPermanentId($permanentId);
        }
    }

    public function getPermanentOrNull()
    {
        if ($this->isPermanent()) {
            return $this->permanentId;
        }
        return null;
    }

    public function isPermanent(): bool
    {
        return $this->permanentId !== null;
    }

    /**
     * @param $id
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function setPermanentId($id)
    {
        $this->guardWrongIdType($id);
        $this->guardAlreadyPermanent();
        $this->permanentId = $id;
    }

    /**
     * @param Id | ModelInterface | int | string $modelOrId
     * @return bool
     */
    public function isEqual($modelOrId): bool
    {
        if ($modelOrId instanceof self) {
            return $modelOrId === $this || $modelOrId->toScalar() == $this->toScalar();
        }

        if ($modelOrId instanceof ModelInterface) {
            /** @var Id $id */
            $id = $modelOrId->getId();
            return $id === $this || $id->toScalar() == $this->toScalar();
        }

        if (is_scalar($modelOrId)) {
            return $modelOrId == $this->toScalar();
        }

        return false;
    }

    public function toScalar()
    {
        return $this->permanentId;
    }

    public function __toString()
    {
        return (string) $this->toScalar();
    }

    /**
     * @param $id
     * @throws InvalidArgumentException
     */
    private function guardWrongIdType($id)
    {
        $isWrong = !is_scalar($id) || is_null($id) || is_bool($id);
        if ($isWrong) {
            throw new InvalidArgumentException('Incorrect permanent Id type');
        }
    }

    /**
     * @throws LogicException
     */
    private function guardAlreadyPermanent()
    {
        if ($this->isPermanent()) {
            throw new LogicException('Id already set');
        }
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->permanentId;
    }
}