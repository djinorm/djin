<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 13:45
 */

namespace DjinORM\Djin\Id;


use DjinORM\Djin\Exceptions\InvalidArgumentException;
use DjinORM\Djin\Exceptions\LogicException;
use DjinORM\Djin\Model\ModelInterface;

class Id
{

    private $tempId;
    private $permanentId;

    public function __construct($permanentId = null)
    {
        if (is_null($permanentId) === false) {
            $this->setPermanentId($permanentId);
        }
    }

    public function getId()
    {
        if ($this->isPermanent()) {
            return $this->permanentId;
        }
        return $this->getTempId();
    }

    public function getPermanentOrNull()
    {
        if ($this->isPermanent()) {
            return $this->permanentId;
        }
        return null;
    }

    public function getTempId()
    {
        if ($this->tempId === null) {
            $this->tempId = uniqid('__DJIN__', true);
        }
        return $this->tempId;
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
            return $modelOrId === $this || $modelOrId->getId() == $this->getId();
        }

        if ($modelOrId instanceof ModelInterface) {
            /** @var Id $id */
            $id = $modelOrId->getId();
            return $id === $this || $id->getId() == $this->getId();
        }

        if (is_scalar($modelOrId)) {
            return $modelOrId == $this->getId();
        }

        return false;
    }

    public function toScalar()
    {
        return $this->getId();
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
            throw new LogicException('Id already setted');
        }
    }

}