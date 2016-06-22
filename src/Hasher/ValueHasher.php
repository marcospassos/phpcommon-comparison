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

use InvalidArgumentException;
use PhpCommon\Comparison\Equatable;
use PhpCommon\Comparison\Equivalence;
use PhpCommon\Comparison\Hasher;
use PhpCommon\Comparison\Hashable;

/**
 * Provides an external means for producing hash codes and comparing values for
 * equality.
 *
 * This equivalence relation delegates the equality check and hashing
 * strategy to the methods {@link PhpCommon\Comparison\Equatable::equals()} and
 * {@link PhpCommon\Comparison\Hashable::getHash()}, whenever the handled
 * values are instances of `Equatable` and `Hashable` respectively.
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class ValueHasher extends IdentityHasher
{
    /**
     * Maps a class name to an equivalence relation.
     *
     * @var Equivalence[]
     */
    protected $equivalences = [];

    /**
     * Creates a new value based equivalence relation.
     *
     * @param array $equivalences The equivalence relation mapping, with class
     *                            names as keys and relations as value.
     */
    public function __construct(array $equivalences = [])
    {
        $this->equivalences = $equivalences;
    }

    /**
     * {@inheritdoc}
     *
     * Two instances are considered equals if they are of the same
     * and if every type-specific relation defined in one is equal to the
     * corresponding relation in the other.
     */
    public function equals(Equatable $other)
    {
        if ($this === $other) {
            return true;
        }

        if (!parent::equals($other)) {
            return false;
        }

        /** @var ValueHasher $other */
        $equivalences = $other->getEquivalences();

        if (count($this->equivalences) !== count($equivalences)) {
            return false;
        }

        foreach ($this->equivalences as $type => $equivalence) {
            if (!isset($equivalences[$type])) {
                return false;
            }

            if (!$equivalence->equals($equivalences[$type])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the type-specific equivalence relations.
     *
     * @return Equivalence[] The type-specific relations, with class names as
     *                       keys and relations as values.
     */
    public function getEquivalences()
    {
        return $this->equivalences;
    }

    /**
     * Returns an equivalence relation suitable for comparing objects of the
     * specified class, if any.
     *
     * When no relation is explicitly defined for the specified class, this
     * method traverses up the class hierarchy to find the nearest ancestor for
     * which a relation is specified. For example, a relation specified for the
     * class `Vehicle` is used to compare instances of its subclass `Car`, when
     * no relation is explicitly specified for it.
     *
     * @param string $className The fully qualified name of the class for which
     *                          the relation should be suitable for.
     *
     * @return Equivalence|boolean The relation suitable for comparing objects
     *                             of the specified class, or `null` no
     *                             suitable relation is found.
     */
    protected function getEquivalence($className)
    {
        if (empty($this->equivalences)) {
            return false;
        }

        if (isset($this->equivalences[$className])) {
            return $this->equivalences[$className];
        }

        if (($parent = get_parent_class($className)) !== false) {
            return $this->getEquivalence($parent);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function equivalentArray(array $left, $right)
    {
        if (!is_array($right) || count($left) !== count($right)) {
            return false;
        }

        foreach ($left as $key => $value) {
            if (!$this->equivalentString($key, key($right))) {
                return false;
            }

            if (!$this->equivalent($value, current($right))) {
                return false;
            }

            next($right);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * The values are considered equivalent if any of the following conditions
     * hold:
     *
     * 1. The reference value is an instance of {@link Equatable} and the
     *    expression `$left->equals($right)` is evaluated to `true`
     * 2. A specific equivalence relation is mapped to the type of the left-hand
     *    value and the expression `$relation->equivalent($left, $right)` is
     *    evaluated to `true`
     * 3. Both values refer to the same instance of the same class (in a
     *    particular namespace)
     */
    protected function equivalentObject($left, $right)
    {
        if ($left instanceof Equatable xor $right instanceof Equatable) {
            return false;
        }

        if ($left instanceof Equatable) {
            return $left->equals($right);
        }

        $equivalence = $this->getEquivalence(get_class($left));

        if ($equivalence !== false) {
            return $equivalence->equivalent($left, $right);
        }

        if (is_object($right)) {
            $equivalence = $this->getEquivalence(get_class($right));

            if ($equivalence !== false) {
                return $equivalence->equivalent($right, $left);
            }
        }

        return parent::equivalentObject($left, $right);
    }

    /**
     * {@inheritdoc}
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentObject()} method, which means that for any references
     * `$x` and `$y`, if `equivalentObject($x, $y)`, then
     * `hashObject($x) === hashObject($y)`.
     *
     * The hash code is computed as follows:
     *
     * 1. If the specified object is an instance of {@link Hashable}, delegates
     *    the hashing strategy to the object being hashed.
     * 2. If a specific equivalence relation of type {@link Hasher} is mapped
     *    to the type of the given object, then uses the method
     *    {@link PhpCommon\Comparison\Hasher::hash()} as the hashing function
     * 3. If none of the previous rules apply, uses the method
     *    {@link PhpCommon\Comparison\Hasher\IdentityHasher::hashObject()} as
     *    the hash function
     *
     * @see hashObject()
     * @see PhpCommon\Comparison\Hashable::getHash()
     */
    protected function hashObject($value)
    {
        if ($value instanceof Hashable) {
            return self::HASH_OBJECT + $value->getHash();
        } elseif ($value instanceof Equatable) {
            throw new InvalidArgumentException(sprintf(
                'Any object implementing %s interface must also implement %s ' .
                'interface, otherwise the resulting hash code cannot be ' .
                'guaranteed by %s to be distributable across equivalences.',
                Equatable::class,
                Hashable::class,
                static::class
            ));
        }

        $equivalence = $this->getEquivalence(get_class($value));

        if ($equivalence instanceof Hasher) {
            return self::HASH_OBJECT + $equivalence->hash($value);
        }

        return parent::hashObject($value);
    }
}
