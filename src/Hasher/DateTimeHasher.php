<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Hasher;

use PhpCommon\Comparison\Equatable;
use PhpCommon\Comparison\Hasher;
use PhpCommon\Comparison\UnexpectedTypeException;
use DateTimeInterface as DateTime;

/**
 * An equivalence relation to determine whether two instances of
 * DateTime have the same date, time and time zone.
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class DateTimeHasher implements Hasher
{
    /**
     * Checks whether the current relation is considered equal to another.
     *
     * Since this class is stateless, its instances will always be considered
     * equal if they are of the same type.
     */
    public function equals(Equatable $other)
    {
        return self::class === get_class($other);
    }

    /**
     * Checks whether the given values are considered equivalent.
     *
     * This equivalence relation considers two instances of {@link DateTime} to
     * be equivalent if they have the same date, time and time zone.
     *
     * @param DateTime $left  The date/time to compare.
     * @param mixed    $right The value to compare.
     *
     * @return boolean Returns `true` if the date/time are considered
     *                 equivalent, `false` otherwise.
     */
    public function equivalent($left, $right)
    {
        $this->assertDateTime($left);

        if (!$right instanceof DateTime) {
            return false;
        }
        
        $leftTimezone = $left->getTimezone();
        $rightTimezone = $right->getTimezone();

        // Compare the date and time
        if ($left->getTimestamp() !== $right->getTimestamp()) {
            return false;
        }

        // Compare the timezone
        return $leftTimezone->getName() === $rightTimezone->getName();
    }

    /**
     * Returns a hash code for the given DateTime instance.
     *
     * The resulting hash code is guaranteed to be _coherent_ with the
     * {@link equivalent()} method, which means that for any references
     * `$x` and `$y`, if `equivalent($x, $y)`, then `hash($x) === hash($y)`.
     * It is computed by summing the values returned by the methods
     * {@link \DateTimeInterface::getTimestamp()} and
     * {@link \DateTimeInterface::getOffset()}, as shown in the following
     * expression:
     *
     * ```php
     * $hashCode = $date->getTimestamp() + $date->getOffset();
     * ```
     *
     * @param DateTime $value The date/time to compare.
     *
     * @return integer The hash code.
     *
     * @see equivalent()
     */
    public function hash($value)
    {
        $this->assertDateTime($value);

        $timezone = $value->getTimezone();

        return $value->getTimestamp() + crc32($timezone->getName());
    }

    /**
     * Asserts the given value is an instance of {@link DateTime}.
     *
     * @param mixed $value The value to assert.
     *
     * @throws UnexpectedTypeException If the given value is not an instance of
     *                                 {@link DateTime}.
     */
    protected function assertDateTime($value)
    {
        if (!$value instanceof DateTime) {
            throw UnexpectedTypeException::forType(DateTime::class, $value);
        }
    }
}
