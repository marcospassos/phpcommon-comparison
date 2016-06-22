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
 * Defines a method that a class implements to provide natural order of sorting
 * to instances.
 *
 * This interface imposes a total ordering on the objects of each class
 * that implements it. This ordering is referred to as the _natural ordering_
 * of the class, and the method {@link compareTo()} is referred to as its
 * _natural comparison method_.
 *
 * The natural ordering for a class `C` is said to be consistent with
 * {@link PhpCommon\Comparison\Equatable::equals()} if and only if
 * `$a->compareTo($b) === 0` has the same boolean value as `$a->equals($b)` for
 * every `$a` and `$b` of class `C`.
 *
 * It is strongly recommended (though not required) that natural orderings
 * be consistent with {@link PhpCommon\Comparison\Equatable::equals()}. This is
 * so because in certain cases these methods can be used together in a
 * complementary way to achieve specific results. For example, some structures,
 * such as sets and maps, may use the methods {@link
 * PhpCommon\Comparison\Equatable::equals()} and {@link compareTo()} as part
 * of the strategy to determine where to store or retrieve data. In that way,
 * such inconsistency may lead to unwanted results.
 *
 * It is inspired by the `Comparable` interface, from Java API.
 *
 * @link   https://docs.oracle.com/javase/7/docs/api/java/lang/Comparable.html
 *         Java Comparable interface
 *
 * @author Marcos Passos <marcos@croct.com>
 */
interface Comparable
{
    /**
     * Compares the current object with another for order.
     *
     * This method has the following properties:
     *
     * * It is _reflexive_: for any instance of {@link Equatable} `$x`,
     *   `$x->compareTo($x) === 0`.
     * * It is _antisymmetric_: for any instances of {@link Equatable} `$x` and
     *   `$y`, if `$y->compareTo($x) <= 0` and `$x->compareTo($y) <= 0`, then
     *   `$x->compareTo($y) === 0`.
     * * It is _transitive_: for any instances of {@link Equatable} `$x`, `$y`,
     *   and `$z`, if `$x->compareTo($y) <= 0` and `$y->compareTo($z) <= 0`,
     *   then `$x->compareTo($z) <= 0`.
     *
     * It is strongly recommended, but not strictly required, that
     * `($x->compareTo($y) === 0) === ($x->equalTo($y))`. Generally speaking,
     * any class that implements the {@link Comparable} interface and violates
     * this condition should clearly indicate this fact. The recommended
     * language is _"Note: this class has a natural ordering that is
     * inconsistent with its natural equality relation."_.
     *
     * @param Comparable $other The object to compare to.
     *
     * @return boolean Returns a negative integer, zero, or a positive integer
     *                 as this object is less than, equal to or greater than
     *                 the specified object.
     *
     * @throws IncomparableException If the specified object is not comparable
     *                               to current object.
     */
    public function compareTo(Comparable $other);
}
