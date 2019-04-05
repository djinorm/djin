<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 04.04.2019 13:48
 */

namespace DjinORM\Djin\Repository;


class Sort
{

    const SORT_ASC = 1;
    const SORT_DESC = -1;

    private $sort = [];

    public function __construct(array $sort = [])
    {
        foreach ($sort as $sortBy => $sortDirection) {
            $this->add($sortBy, $sortDirection);
        }
    }

    public function get(): array
    {
        return $this->sort;
    }

    public function add(string $sortBy, int $sortDirection = self::SORT_DESC)
    {
        $this->guardSortDirection($sortDirection);
        $this->sort[$sortBy] = $sortDirection;
    }

    public function clear()
    {
        $this->sort = [];
    }

    private function guardSortDirection(int $sort)
    {
        if (!in_array($sort, [self::SORT_ASC, self::SORT_DESC])) {
            throw new \InvalidArgumentException("Invalid sort direction «{$sort}»");
        }
    }

}