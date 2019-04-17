<?php
/**
 * Created for DjinORM.
 * Datetime: 16.04.2019 12:03
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Repository;


class Paginator
{

    protected $number;
    protected $size;

    public function __construct(int $number, int $size)
    {
        $this->number = $number;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Paginator
     */
    public function setNumber(int $number): Paginator
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return Paginator
     */
    public function setSize(int $size): Paginator
    {
        $this->size = $size;
        return $this;
    }

}