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
 * Defines a method that a class implements to provide hashing functionality.
 *
 * Hashes are very important as a way to efficiently lookup objects in
 * hash-based data structures. This interface aims to facilitate the use of
 * those structures by allowing classes to define custom hashing strategies.
 *
 * @author Marcos Passos <marcos@croct.com>
 */
interface Hashable extends Equatable
{
    /**
     * Returns a hash code for the current object.
     *
     * This method has the following properties:
     *
     * * It is _consistent_: for any `$x`, multiple invocations of
     *   `$x->getHash()` consistently return the same value provided that `$x`
     *   remains unchanged according to the definition of the equivalence. The
     *   hash need not remain consistent from one execution of an application
     *   to another execution of the same application.
     * * It is _distributable across equivalence_: for any instance of
     *   {@link Hashable} `$x` and `$y`, if `$x->equals($y)`, then
     *   `$x->getHash() === $y->getHash()`. It is not necessary that the hash
     *   be distributable across _inequivalence_. If `$x->equals($y)` is
     *   `false`, `$x->getHash() === $y->getHash()` may still be `true`.
     *
     * When implementing this method it is recommended to use a fast algorithm
     * that produces reasonably different results for unequal values, and
     * shift the heavy comparison logic to
     * {@link PhpCommon\Comparison\Equatable::equals()}.
     *
     * @return integer The hash code for the current object.
     */
    public function getHash();
}
