<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 11:39
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\TestHelpers;


use DjinORM\Djin\Id\Id;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

class IdComparator extends Comparator
{

    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual)
    {
        return is_a($expected, Id::class) && is_a($actual, Id::class);
    }

    /**
     * Asserts that two values are equal.
     *
     * @param Id $expected First value to compare
     * @param Id $actual Second value to compare
     * @param float $delta Allowed numerical distance between two values to consider them equal
     * @param bool $canonicalize Arrays are sorted before comparison when set to true
     * @param bool $ignoreCase Case is ignored when set to true
     *
     * @throws ComparisonFailure
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        if (!$expected->isEqual($actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $expected->toScalar(),
                $actual->toScalar(),
                false,
                'Failed asserting that two Id objects are equal.'
            );
        }
    }
}