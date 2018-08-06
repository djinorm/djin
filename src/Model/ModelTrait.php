<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 18.03.2017 2:27
 */

namespace DjinORM\Djin\Model;


use DjinORM\Djin\Id\Id;

trait ModelTrait
{

    public static function getModelIdPropertyName():string
    {
        return 'id';
    }

    /**
     * @return Id
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function getId(): Id
    {
        $property = static::getModelIdPropertyName();
        $value = $this->{$property};
        if (is_scalar($value) || is_null($value)) {
            $this->{$property} = new Id($value);
        }
        return $this->{$property};
    }

    /**
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     */
    public function __clone()
    {
        $property = static::getModelIdPropertyName();
        $this->{$property} = new Id();
    }

}