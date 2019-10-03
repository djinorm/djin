<?php
/**
 * Created for DjinORM.
 * Datetime: 31.10.2017 12:03
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DateTime;
use DateTimeInterface;
use DjinORM\Djin\Exceptions\SerializerException;

class DateTimeMapper implements MapperInterface
{
    /**
     * @var string
     */
    private $format;

    public function __construct($format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    /**
     * Превращает сложный обект в простой тип (scalar, null, array)
     * @param DateTimeInterface $complex
     * @return string|null
     * @throws SerializerException
     */
    public function serialize($complex)
    {
        if (!($complex instanceof DateTimeInterface)) {
            $type = gettype($complex);
            throw new SerializerException("Can not serialize {$this->classname()} from '{$type}' type");
        }
        return $complex->format($this->format);
    }

    /**
     * Превращает простой тип (scalar, null, array) в сложный (object)
     * @param string|null $data
     * @return DateTimeInterface
     * @throws SerializerException
     */
    public function deserialize($data)
    {
        if (!is_string($data)) {
            $type = gettype($data);
            throw new SerializerException("{$this->classname()} can not be hydrated from '{$type}' type");
        }

        /** @var DateTime $classname */
        $classname = $this->classname();
        $datetime = $classname::createFromFormat($this->format, $data);
        if (!$datetime) {
            throw new SerializerException("{$this->classname()} can not be hydrated from string '{$data}'");
        }

        return $datetime;
    }

    /**
     * @return string
     */
    protected function classname(): string
    {
        return DateTime::class;
    }
}