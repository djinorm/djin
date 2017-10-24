<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 26.06.2017 1:30
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use ReflectionClass;
use ReflectionProperty;

class RepoHelper
{

    /** @var ReflectionClass[] */
    private static $reflectionClasses = [];

    /** @var ReflectionProperty[] */
    private static $reflectionProperties = [];

    public static function newWithoutConstructor(string $class)
    {
        if (!isset(self::$reflectionClasses[$class])) {
            self::$reflectionClasses[$class] = new ReflectionClass($class);
        }
        return self::$reflectionClasses[$class]->newInstanceWithoutConstructor();
    }

    public static function getProperty($object, string $property)
    {
        $className = get_class($object);
        $refProperty = self::getReflectionProperty($className, $property);
        return $refProperty->getValue($object);
    }

    public static function setProperty($object, string $property, $value)
    {
        $className = get_class($object);
        $refProperty = self::getReflectionProperty($className, $property);
        $refProperty->setValue($object, $value);
    }

    /**
     * @param ModelInterface $object
     * @param string $property
     * @param array|string|int $data
     */
    public static function setIdFromScalar($object, string $property, $data)
    {
        $id = is_array($data) ? $data[$property] : $data;
        self::setProperty($object, $property, new Id($id));
    }

    /**
     * @param $object
     * @param string $property
     * @param array|string $data
     * @param bool $immutable
     */
    public static function setDateTime($object, string $property, $data, $immutable = true)
    {
        $class = $immutable ? \DateTimeImmutable::class : \DateTime::class;
        $datetime = is_array($data) ? $data[$property] : $data;
        self::setProperty($object, $property, new $class($datetime));
    }

    /**
     * @param $object
     * @param string $property
     * @param array|string $data
     */
    public static function setString($object, string $property, $data)
    {
        $string = is_array($data) ? $data[$property] : $data;
        self::setProperty($object, $property, (string) $string);
    }

    /**
     * @param $object
     * @param string $property
     * @param array|string|int $data
     */
    public static function setInt($object, string $property, $data)
    {
        $int = is_array($data) ? $data[$property] : $data;
        self::setProperty($object, $property, (int) $int);
    }

    /**
     * @param $object
     * @param string $property
     * @param array|string|int|float $data
     */
    public static function setFloat($object, string $property, $data)
    {
        $float = is_array($data) ? $data[$property] : $data;
        self::setProperty($object, $property, (float) $float);
    }

    /**
     * @param $object
     * @param string $property
     * @param array|string|int|bool $data
     */
    public static function setBool($object, string $property, $data)
    {
        $bool = is_array($data) ? $data[$property] : $data;
        self::setProperty($object, $property, (bool) $bool);
    }

    private static function getReflectionProperty($className, string $property)
    {
        $key = $className . '::' . $property;
        if (!isset(self::$reflectionProperties[$key])) {
            self::$reflectionProperties[$key] = new ReflectionProperty($className, $property);
        }
        self::$reflectionProperties[$key]->setAccessible(true);
        return self::$reflectionProperties[$key];
    }

}