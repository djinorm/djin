<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 26.06.2017 1:30
 */

namespace DjinORM\Djin\Helpers;


use DjinORM\Djin\Id\Id;
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

    public static function getProperty($object, $property)
    {
        $className = get_class($object);
        $refProperty = self::getReflectionProperty($className, $property);
        return $refProperty->getValue($object);
    }

    public static function setProperty($object, $property, $value)
    {
        $className = get_class($object);
        $refProperty = self::getReflectionProperty($className, $property);
        $refProperty->setValue($object, $value);
    }

    public static function setIdFromScalar($object, $property, array $data)
    {
        self::setProperty($object, $property, new Id((int) $data[$property]));
    }

    public static function setDateTime($object, $property, array $data, $immutable = true)
    {
        $class = $immutable ? \DateTimeImmutable::class : \DateTime::class;
        self::setProperty($object, $property, new $class($data[$property]));
    }

    public static function setString($object, $property, array $data)
    {
        self::setProperty($object, $property, (string)$data[$property]);
    }

    public static function setInt($object, $property, array $data)
    {
        self::setProperty($object, $property, (int) $data[$property]);
    }

    public static function setFloat($object, $property, array $data)
    {
        self::setProperty($object, $property, (float) $data[$property]);
    }

    public static function setBool($object, $property, array $data)
    {
        self::setProperty($object, $property, (bool) $data[$property]);
    }

    private static function getReflectionProperty($className, $property)
    {
        $key = $className . '::' . $property;
        if (!isset(self::$reflectionProperties[$key])) {
            self::$reflectionProperties[$key] = new ReflectionProperty($className, $property);
        }
        self::$reflectionProperties[$key]->setAccessible(true);
        return self::$reflectionProperties[$key];
    }

}