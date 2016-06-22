<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Equivalence;

use PhpCommon\Comparison\Equivalence;
use InvalidArgumentException;

/**
 * Base class for implementations of generic equivalence relations.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
abstract class GenericEquivalence implements Equivalence
{
    /**
     * Constant that represents the primitive type array .
     */
    const TYPE_ARRAY = 'array';

    /**
     * Constant that represents the primitive type boolean.
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Constant that represents the primitive type double.
     */
    const TYPE_DOUBLE = 'double';

    /**
     * Constant that represents the primitive type integer.
     */
    const TYPE_INTEGER = 'integer';

    /**
     * Constant that represents the primitive type NULL.
     */
    const TYPE_NULL = 'NULL';

    /**
     * Constant that represents the primitive type object.
     */
    const TYPE_OBJECT = 'object';

    /**
     * Constant that represents the primitive type resource.
     */
    const TYPE_RESOURCE = 'resource';

    /**
     * Constant that represents the primitive type string.
     */
    const TYPE_STRING = 'string';

    /**
     * {@inheritdoc}
     */
    public function equivalent($left, $right)
    {
        // Delegates the call to the proper equivalent*() method
        $type = gettype($left);

        switch ($type) {
            case self::TYPE_ARRAY:
                return $this->equivalentArray($left, $right);

            case self::TYPE_BOOLEAN:
                return $this->equivalentBoolean($left, $right);

            case self::TYPE_DOUBLE:
                return $this->equivalentFloat($left, $right);

            case self::TYPE_INTEGER:
                return $this->equivalentInteger($left, $right);

            case self::TYPE_NULL:
                return $this->equivalentNull($right);

            case self::TYPE_OBJECT:
                return $this->equivalentObject($left, $right);

            case self::TYPE_RESOURCE:
                return $this->equivalentResource($left, $right);

            case self::TYPE_STRING:
                return $this->equivalentString($left, $right);
        }

        // This exception should never be thrown unless a new primitive type
        // was introduced
        throw new InvalidArgumentException(
            sprintf('Unknown type "%s".', $type)
        );
    }

    /**
     * Checks whether an array is equivalent to another value.
     *
     * @param array $left  The array to compare.
     * @param mixed $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.array.php PHP array
     */
    abstract protected function equivalentArray(array $left, $right);

    /**
     * Checks whether a boolean value is equivalent to another value.
     *
     * @param boolean $left  The boolean value to compare.
     * @param mixed   $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.boolean.php PHP boolean
     */
    abstract protected function equivalentBoolean($left, $right);

    /**
     * Checks whether a floating-point number is equivalent to another value.
     *
     * @param float $left  The floating-point number to compare.
     * @param mixed $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.float.php PHP float
     */
    abstract protected function equivalentFloat($left, $right);

    /**
     * Checks whether an integer number is equivalent to another value.
     *
     * @param integer $left  The integer number to compare.
     * @param mixed   $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.integer.php PHP integer
     */
    abstract protected function equivalentInteger($left, $right);

    /**
     * Checks whether value is equivalent to null.
     *
     * @param mixed $right The value to compare.
     *
     * @return boolean Returns `true` if the given value is considered
     *                 equivalent to null, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.null.php PHP NULL
     */
    abstract protected function equivalentNull($right);

    /**
     * Checks whether an object is equivalent to another value.
     *
     * @param object $left  The object to compare.
     * @param mixed  $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.object.php PHP object
     */
    abstract protected function equivalentObject($left, $right);

    /**
     * Checks whether a resource is equivalent to another value.
     *
     * @param resource $left  The resource to compare.
     * @param mixed    $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.resource.php PHP resource
     */
    abstract protected function equivalentResource($left, $right);

    /**
     * Checks whether a string is equivalent to another value.
     *
     * @param string $left  The string value to compare.
     * @param mixed  $right The other value to compare.
     *
     * @return boolean Returns `true` if the given values are considered
     *                 equivalent, `false` otherwise.
     *
     * @link http://php.net/manual/en/language.types.string.php PHP string
     */
    abstract protected function equivalentString($left, $right);
}
