<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison;

/**
 * A strategy for sorting values.
 *
 * A comparison function, which imposes a total ordering on some collection of
 * objects. Comparators can be passed to a sort method of a collection to allow
 * precise control over its sort order. It can also be used to control the
 * order of certain data structures, such as sorted sets or sorted maps.
 *
 * The ordering imposed by a {@link Ordering} `$c` on a set of elements `S` is
 * said to be consistent with an {@link Equivalence} `$e` if and only if
 * `$c->compare($a, $b) === 0` has the same boolean value as
 * `$e->equivalent($a, $b)` for every `$a` and `$b` in `S`.
 *
 * It is strongly recommended, though not required, that natural orderings
 * be consistent with {@link Equatable::equals()}. This is so because in
 * certain cases these methods can be used together in a complementary way to
 * achieve specific results. For example, some structures, such as sets and
 * maps, may use both {@link Equatable::equals()} and
 * {@link Ordering::compare()} methods as part of the strategy to determine
 * where to store or retrieve data. In the way, such inconsistency may lead to
 * unwanted results.
 *
 * It is inspired by the Comparator interface, from Java API.
 *
 * @author Marcos Passos <marcos@croct.com>
 *
 * @link   https://docs.oracle.com/javase/7/docs/api/java/util/Comparator.html
 *         Java Comparator interface
 */
interface Comparator
{
    /**
     * Compares two values for order.
     *
     * This method has the following properties:
     *
     * * It is _reflexive_: for any `$x`, `compare($x, $x) === 0`.
     * * It is _antisymmetric_: for any `$x` and `$y`, if
     *   `compare($x, $y) <= 0` and `compare($y, $x) <= 0`,
     *   then `compare($x, $y) === 0`.
     * * It is _transitive_: for any values `$x`, `$y`, and `$z`,
     *   if `compare($x, $y) <= 0` and `compare($y, $z) <= 0`, then
     *   `compare($x, $z) <= 0`.
     *
     * It is strongly recommended, but _not_ strictly required, that
     * `(compare($x, $y) === 0) === ($x->equals($y))`. Generally speaking, any
     * ordering that violates this condition should clearly indicate this
     * fact. The recommended language is _"Note: this class imposes an ordering
     * that is inconsistent with the natural equivalence relation of the
     * values."_.
     *
     * @param mixed $left  The left-hand side to compare with.
     * @param mixed $right The right-hand side to compare with.
     *
     * @return integer Returns a negative integer, zero, or a positive integer
     *                 as the left-hand side is less than, equal to or greater
     *                 than the right-hand side.
     *
     * @throws UnexpectedTypeException If the type of the left-hand side does
     *                                 not match the expected type.
     * @throws IncomparableException   If the specified values are not
     *                                 comparable under the current order
     *                                 relation.
     */
    public function compare($left, $right);
}
