<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 20.06.2017 13:45
 */

namespace DjinORM\Djin\Id;


use DjinORM\Djin\Model\ModelInterface;
use JsonSerializable;

class Id implements JsonSerializable
{

    /** @var string|null */
    private $permanentId;

    /**
     * Id constructor.
     * @param string $permanentId
     */
    public function __construct(string $permanentId = null)
    {
        if (is_null($permanentId) === false) {
            $this->assign($permanentId);
        }
    }

    /**
     * @return string|null
     * @deprecated
     */
    public function getPermanentOrNull(): ?string
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
     * @param string $id
     * @return bool
     */
    public function assign(string $id): bool
    {
        if (!$this->isPermanent()) {
            $this->permanentId = $id;
            return true;
        }
        return false;
    }

    /**
     * @param string|null $permanentId
     * @return bool
     * @deprecated
     */
    public function setPermanentId($permanentId): bool
    {
        return $this->assign($permanentId);
    }

    /**
     * @param Id | ModelInterface | int | string $modelOrId
     * @return bool
     */
    public function isEqual($modelOrId): bool
    {
        if ($modelOrId instanceof self) {
            return $modelOrId === $this || $modelOrId->toString() == $this->toString();
        }

        if ($modelOrId instanceof ModelInterface) {
            /** @var Id $id */
            $id = $modelOrId->getId();
            return $id === $this || $id->toString() == $this->toString();
        }

        if (is_scalar($modelOrId)) {
            return $modelOrId == $this->toString();
        }

        return false;
    }

    public function toString(): ?string
    {
        return $this->permanentId;
    }

    public function __toString()
    {
        return (string) $this->toString();
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