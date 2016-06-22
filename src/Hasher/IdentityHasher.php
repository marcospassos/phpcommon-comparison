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
use PhpCommon\IntMath\IntMath as Math;

/**
 * Provides an external means for producing hash codes and comparing values for
 * identity.
 *
 * This equivalence relation uses the strict equal operator (`===`) to compare
 * values, while hashing strategy varies according to the type of value.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
class IdentityHasher extends GenericHasher
{
    /**
     * Constant used to compute hash codes for `null`
     */
    const HASH_NULL = 0;
    
    /**
     * Constant used to compute hash codes for arrays
     */
    const HASH_ARRAY = 991;

    /**
     * The hash code for the boolean value `false`
     */
    const HASH_FALSE = 1237;

    /**
     * The hash code for the boolean value `true`
     */
    const HASH_TRUE = 1231;

    /**
     * Constant used to compute hash codes for objects
     */
    const HASH_OBJECT = 1093;

    /**
     * Constant used to compute hash codes for resources
     */
    const HASH_RESOURCE = 1471;

    /**
     * Constant used to compute hash codes for strings
     */
    const HASH_STRING = 1321;

    /**
     * Checks whether the current relation is considered equal to another.
     *
     * Since this class is stateless, its instances will always be considered
     * equal if they are of the same type.
     */
    public function equals(Equatable $other)
    {
        return static::class === get_class($other);
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also an array and they both contain the same
     * number of entries, in the same order and each pair of corresponding
     * entries is equivalent according to this relation. Empty arrays are
     * equivalent to one another.
     */
    protected function equivalentArray(array $left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also a boolean and both are `true` or both are
     * `false`.
     */
    protected function equivalentBoolean($left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also a float and they are numerically equal
     * (have the same number value). Positive and negative zeros are equal
     * to one another. {@link NAN} is not equal to anything, including
     * {@link NAN}.
     */
    protected function equivalentFloat($left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also an integer and they are numerically equal
     * (have the same number value). Positive and negative zeros are equal
     * to one another.
     */
    protected function equivalentInteger($left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified value is considered equivalent to `null` if and only if it
     * is strictly equal to `null`.
     */
    protected function equivalentNull($right)
    {
        return null === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also an object and both are references to the
     * same instance.
     */
    protected function equivalentObject($left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also a resource and both have the same unique
     * resource number.
     */
    protected function equivalentResource($left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The specified values are considered equivalent if and only if the
     * right-hand value is also a string and both have the same sequence of
     * characters.
     */
    protected function equivalentString($left, $right)
    {
        return $left === $right;
    }

    /**
     * {@inheritdoc}
     *
     * The hash code is based on the _deep contents_ of the specified array.
     * More precisely, it is computed as follows:
     *
     * ```php
     * $hashCode = IdentityEquivalence::HASH_ARRAY;
     * foreach ($array => $key => $element)
     *     $hashCode = 31 * $hashCode + (hash($key) ^ hash($element));
     * ```
     *
     * Where `hash()` is a function that returns a hash code for a given value.
     * Note that the hash code of an empty array is the constant
     * {@link IdentityEquivalence::HASH_ARRAY} itself.
     */
    protected function hashArray(array $value)
    {
        $hash = self::HASH_ARRAY;

        foreach ($value as $key => $current) {
            $keyHash = $this->hashString($key);
            $valueHash = $this->hash($current);

            $hash = $hash * 31 + ($keyHash ^ $valueHash);
        }

        return $hash;
    }

    /**
     * {@inheritdoc}
     *
     * It is defined by mapping the values `true` and `false` to
     * integer numbers defined by {@link IdentityEquivalence::HASH_TRUE} and
     * {@link IdentityEquivalence::HASH_FALSE} respectively.
     */
    protected function hashBoolean($value)
    {
        return $value ? self::HASH_TRUE : self::HASH_FALSE;
    }

    /**
     * {@inheritdoc}
     *
     * It is computed by converting the float value to the integer bit
     * representation, according to the IEEE 754 floating-point single format
     * bit layout.
     *
     * @link https://pt.wikipedia.org/wiki/IEEE_754 IEEE Standard for
     *       Floating-Point Arithmetic.
     *
     */
    protected function hashFloat($value)
    {
        return unpack('i', pack('f', $value))[1];
    }

    /**
     * {@inheritdoc}
     *
     * This method uses the integer number as the hash code itself.
     */
    protected function hashInteger($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * The hash code for `null` is a constant defined by
     * {@link IdentityEquivalence::HASH_NULL}.
     */
    protected function hashNull()
    {
        return self::HASH_NULL;
    }

    /**
     * {@inheritdoc}
     *
     * The hash code is computed as follows:
     *
     * ```php
     * $k = IdentityEquivalence::HASH_OBJECT;
     * $hashCode = $k * hashString(spl_object_hash($object));
     * ```
     *
     * Where {@link IdentityEquivalence::HASH_OBJECT} is a constant and
     * `hashString()` is a function that returns a hash code for a given
     * string.
     */
    protected function hashObject($value)
    {
        return self::HASH_OBJECT * $this->hashString(spl_object_hash($value));
    }

    /**
     * {@inheritdoc}
     *
     * The hash code is computed as follows:
     *
     * ```php
     * $hashCode = IdentityEquivalence::HASH_STRING;
     * for ($i = 0; $i < length($string); $i++)
     *     $hashCode = $hashCode * 31 + charCode($string[$i]);
     * ```
     *
     * Where {@link IdentityEquivalence::HASH_STRING} is a constant, `$i` is
     * the position of the current character in the string, `$string[$i]` is
     * the ith character of the string, `length()` is a function that returns
     * the length of the string, `charCode()` is a function that returns the
     * ASCII code (as an integer) of a given character, and `^` indicates
     * exponentiation. Note that the hash code of a zero length string is the
     * constant {@link IdentityEquivalence::HASH_STRING} itself.
     */
    protected function hashString($value)
    {
        $hash = self::HASH_STRING;
        $value = strval($value);
        $length = strlen($value);

        if ($length === 0) {
            return $hash;
        }

        for ($i = 0; $i < $length; $i++) {
            $hash = Math::add(Math::multiply($hash, 31), ord($value[$i]));
        }

        return $hash;
    }

    /**
     * {@inheritdoc}
     *
     * The hash code is computed as follows:
     *
     * ```php
     * $k = IdentityEquivalence::HASH_RESOURCE;
     * $hashCode =  $k * (1 + resourceId($value));
     * ```
     *
     * Where `$k` is a constant defined by
     * {@link IdentityEquivalence::HASH_RESOURCE} and `resourceId()` is a
     * function that returns the unique resource number assigned to the
     * resource by PHP at runtime.
     */
    protected function hashResource($value)
    {
        return self::HASH_RESOURCE * (1 + (int) $value);
    }
}
