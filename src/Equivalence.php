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
 * A strategy for determining whether two values are considered equivalent.
 *
 * It is important to distinguish between a type that can be compared for
 * equality and a representation of an equivalence relation. This interface is
 * for representing the latter, while {@link Equatable} is for representing the
 * former.
 *
 * For the purpose of this interface, an equivalence relation is a binary
 * relation that is _reflexive_, _symmetric_, _transitive_ and _consistent_.
 * Additionally, an equivalence relation can be either generic or
 * type-specific. The _"equals"_ relation is an example of a generic relation.
 * Type-specific examples include:
 *
 * * _"Has the same birthday as"_ on the set of all people.
 * * _"Is similar to" or "congruent to"_ on the set of all triangles.
 * * _"Has the same absolute value"_ on the set of real numbers.
 *
 * Consult the documentation of the specific {@link Equivalence} implementation
 * for more information about the supported types.
 *
 * It is inspired by the `Equivalence` interface, from Guava API.
 *
 * @link   http://google.github.io/guava/releases/16.0/api/docs/com/google/common/base/Equivalence.html
 *         Guava Equivalent interface
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
interface Equivalence extends Equatable
{
    /**
     * Checks whether the specified values are considered equivalent.
     *
     * This method has the following properties:
     *
     * * It is _reflexive_: for any `$x`, including `null`,
     *   `equivalent($x, $x)` returns `true`.
     * * It is symmetric: for any `$x` and `$y`,
     *   `equivalent($x, $y) === equivalent($y, $x)`.
     * * It is _transitive_: for any references `$x`, `$y`, and `$z`, if
     *   `equivalent($x, $y)` returns `true` and `equivalent($y, $z)` returns
     *   `true`, then `equivalent($x, $z)` returns `true`.
     * * It is _consistent_: for any `$x` and `$y`, multiple invocations of
     *   `equivalent($x, $y)` consistently return `true` or consistently return
     *   `false` (provided that neither `$x` nor `$y` is modified).
     *
     * @link https://en.wikipedia.org/wiki/Equivalence_relation Equivalence
     *                                                          relation
     *
     * @param mixed $left  The left-hand value to compare.
     * @param mixed $right The right-hand value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @throws UnexpectedTypeException If the type of the left-hand side does
     *                                 not match the expected type.
     * @throws IncomparableException  If the given values are not comparable
     *                                 under the specified equivalence relation.
     */
    public function equivalent($left, $right);
}
