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
 * Defines a method that a class implements to determine equality of instances.
 *
 * For the purpose of this interface, an equivalence relation is a binary
 * relation that is _reflexive_, _symmetric_, _transitive_ and _consistent_.
 * This equivalence relation is exposed as the {@link Equatable::equals()}
 * method.
 *
 * It is important to distinguish between a type that can be compared for
 * equality and a representation of an equivalence relation. This interface is
 * for representing the former, while {@link Equivalence} is for representing
 * the latter.
 *
 * @author Marcos Passos <marcos@croct.com>
 */
interface Equatable
{
    /**
     * Checks whether the current object is considered equal to another.
     *
     * The `equals` method implements an equivalence relation on non-null
     * object references. This relation has the following properties:
     *
     * * It is _reflexive_: for any non-null reference value `$x`,
     *   `$x->equals($x)` should return `true`.
     * * It is _symmetric_: for any non-null reference values `$x` and `$y`,
     *  `$x->equals($y)` should return `true` if and only if `$y->equals($x)`
     *   returns `true`.
     * * It is _transitive_: for any non-null reference values `$x`, `$y`, and
     *   `$z`, if `$x->equals($y)` returns `true` and `$y->equals($z)`
     *   returns `true`, then `$x->equals($z)` should return `true`.
     * * It is _consistent_: for any non-null reference values `$x` and `$y`,
     *   multiple invocations of `$x->equals($y)` consistently return `true`
     *   or consistently return `false`, provided `$x` and `$y` remain
     *   unchanged according to the definition of the equality.
     *
     * @param Equatable $other The value to compare to.
     *
     * @return boolean Returns `true` if the current object is equal to the
     *                 specified object, `false` otherwise.
     */
    public function equals(Equatable $other);
}
