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

use InvalidArgumentException;

/**
 * A strategy for hashing values and comparing them for equivalence.
 *
 * The {@link hash()} method, introduced by this interface, is intended
 * to provide a means for performing fast _inequivalence_ checks and efficient
 * insertion and lookup in hash-based data structures. This method is always
 * _consistent_ with {@link PhpCommon\Comparison\Equivalence::equivalent()},
 * which means that for any references `$x` and `$y`, if `equivalent($x, $y)`,
 * then `hash($x) === hash($y)`. However, if `equivalence($x, $y)` evaluates to
 * `false`, `hash($x) === hash($y)` may still be true. Hence why the `hash()`
 * method is suitable for _inequivalence_ checks, but not _equivalence_ checks.
 *
 * In accordance with {@link Equivalence}, a {@link Hasher} can be either
 * generic or type-specific. For that reason, caution should be exercised to
 * ensure the values passed to the methods
 * {@link PhpCommon\Comparison\Equivalence::equivalent()} and {@link hash()}
 * match the type of values supported by the implementing class. Otherwise, an
 * exception may be thrown.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
interface Hasher extends Equivalence
{
    /**
     * Returns a hash code for the given value.
     *
     * This method has the following properties:
     *
     * * It is _consistent_: for any `$x`, multiple invocations of `hash($x)`
     *   consistently return the same value provided `$x` remains unchanged
     *   according to the definition of the equivalence. The hash need not
     *   remain consistent from one execution of an application to another
     *   execution of the same application.
     * * It is _distributable across equivalence_: for any `$x` and `$y`, if
     *   `equivalent($x, $y)`, then `hash($x) === hash($y)`. It is not
     *   necessary that the hash be distributable across _inequivalence_. If
     *   `equivalence($x, $y)` is `false`, `hash($x) === hash($y)` may still be
     *   `true`.
     *
     * @param mixed $value The value to hash.
     *
     * @return integer The hash code for the given value.
     *
     * @throws UnexpectedTypeException  If the type of the specified value does
     *                                  not match the expected type.
     * @throws InvalidArgumentException If some property of the specified value
     *                                  prevents it from being hashed.
     *
     * @see Equivalence               Equivalence relations
     * @see Equivalence::equivalent() The equivalent() method
     */
    public function hash($value);
}
