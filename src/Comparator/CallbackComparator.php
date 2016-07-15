<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Comparator;

use PhpCommon\Comparison\Comparator;

/**
 * A comparator the uses a callback to compare values.
 *
 * This comparator is useful when simple, one-off comparators need to be
 * created. It can be used to change or extend existing comparators in order to
 * achieve new ones that better fit to the underlying needs.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
class CallbackComparator implements Comparator
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * CallbackComparator constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function compare($left, $right)
    {
        return call_user_func_array($this->callback, [$left, $right]);
    }
}
