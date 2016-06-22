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

use Exception;
use InvalidArgumentException;

/**
 * Thrown when a value does not match an expected type.
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class UnexpectedTypeException extends InvalidArgumentException
{
    /**
     * Creates a new exception for the given value type.
     *
     * @param string         $type  The name of the expected type.
     * @param mixed          $value The specified value.
     * @param int            $code  The exception code.
     * @param Exception|null $cause The exception that caused this exception.
     *
     * @return UnexpectedTypeException The new exception.
     */
    public static function forType($type, $value, $code = 0, Exception $cause = null)
    {
        return new self(
            sprintf(
                'Expected value of type "%s", given "%s".',
                $type,
                is_object($value) ? get_class($value) : gettype($value)
            ),
            $code,
            $cause
        );
    }
}
