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

use PhpCommon\Comparison\Equivalence\GenericEquivalence;
use PhpCommon\Comparison\Hasher;
use InvalidArgumentException;

/**
 * Base class for implementing generic hashers.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
abstract class GenericHasher extends GenericEquivalence implements Hasher
{
    /**
     * {@inheritdoc}
     */
    public function hash($value)
    {
        // Delegates the call to the proper hash*() method
        $type = gettype($value);

        switch ($type) {
            case self::TYPE_ARRAY:
                return $this->hashArray($value);

            case self::TYPE_BOOLEAN:
                return $this->hashBoolean($value);

            case self::TYPE_DOUBLE:
                return $this->hashFloat($value);

            case self::TYPE_INTEGER:
                return $this->hashInteger($value);

            case self::TYPE_NULL:
                return $this->hashNull();

            case self::TYPE_OBJECT:
                return $this->hashObject($value);

            case self::TYPE_RESOURCE:
                return $this->hashResource($value);

            case self::TYPE_STRING:
                return $this->hashString($value);
        }

        // This exception should never be thrown unless a new primitive type
        // was introduced
        throw new InvalidArgumentException(
            sprintf('Unknown type "%s".', $type)
        );
    }

    /**
     * Returns a hash code for the given array.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentArray()} method, which means that for any references
     * `$x` and `$y`, if `equivalentArray($x, $y)`, then
     * `hashArray($x) === hashArray($y)`.
     *
     * @param array $value The array to hash.
     *
     * @return integer The hash code for the given array.
     *
     * @link http://php.net/manual/en/language.types.array.php PHP array
     */
    abstract protected function hashArray(array $value);

    /**
     * Returns a hash code for the given boolean value.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentBoolean()} method, which means that for any references
     * `$x` and `$y`, if `equivalentBoolean($x, $y)`, then
     * `hashBoolean($x) === hashBoolean($y)`.
     *
     * @param boolean $value The boolean value to hash.
     *
     * @return integer The hash code for the given boolean value.
     *
     * @link http://php.net/manual/en/language.types.boolean.php PHP boolean
     */
    abstract protected function hashBoolean($value);

    /**
     * Returns a hash code for the given floating-point number.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentFloat()} method, which means that for any references
     * `$x` and `$y`, if `equivalentFloat($x, $y)`, then
     * `hashFloat($x) === hashFloat($y)`.
     *
     * @param float $value The floating-point number to hash.
     *
     * @return integer The hash code for the given floating-point number.
     *
     * @link http://php.net/manual/en/language.types.float.php PHP float
     */
    abstract protected function hashFloat($value);

    /**
     * Returns a hash code for the given integer number.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentInteger()} method, which means that for any references
     * `$x` and `$y`, if `equivalentInteger($x, $y)`, then
     * `hashInteger($x) === hashInteger($y)`.
     *
     * @param integer $value The integer number to hash.
     *
     * @return integer The hash code for the given integer number.
     *
     * @link http://php.net/manual/en/language.types.integer.php PHP interger
     */
    abstract protected function hashInteger($value);

    /**
     * Returns a hash code for the null value.
     *
     * @return integer The hash code for the `NULL` value.
     *
     * @link http://php.net/manual/en/language.types.null.php PHP NULL
     */
    abstract protected function hashNull();

    /**
     * Returns a hash code for the given object.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentObject()} method, which means that for any references
     * `$x` and `$y`, if `equivalentObject($x, $y)`, then
     * `hashObject($x) === hashObject($y)`.
     *
     * @param object $value The object to hash.
     *
     * @return integer The hash code for the given object.
     *
     * @link http://php.net/manual/en/language.types.object.php PHP object
     */
    abstract protected function hashObject($value);

    /**
     * Returns a hash code for the given resource.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentResource()} method, which means that for any references
     * `$x` and `$y`, if `equivalentResource($x, $y)`, then
     * `hashResource($x) === hashResource($y)`.
     *
     * @param resource $value The resource to hash.
     *
     * @return integer The hash code for the given resource.
     *
     * @link http://php.net/manual/en/language.types.resource.php PHP resource
     */
    abstract protected function hashResource($value);

    /**
     * Returns a hash code for the given string value.
     *
     * The resulting hash code is guaranteed to be _consistent_ with the
     * {@link equivalentString()} method, which means that for any references
     * `$x` and `$y`, if `equivalentString($x, $y)`, then
     * `hashString($x) === hashString($y)`.
     *
     * @param string $value The string value to hash.
     *
     * @return integer The hash code for the given object.
     *
     * @link http://php.net/manual/en/language.types.string.php PHP string
     */
    abstract protected function hashString($value);
}
