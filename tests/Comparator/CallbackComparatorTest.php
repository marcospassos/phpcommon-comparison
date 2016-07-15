<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Tests\Comparator;

use PhpCommon\Comparison\Comparator\CallbackComparator;
use PHPUnit_Framework_TestCase;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class CallbackComparatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @testdox The compare() method delegates calls to the callback function
     */
    public function testCompareMethodDelegatesCallsToTheCallbackFunction()
    {
        $callback = $this->getMockBuilder('stdClass')
            ->setMethods(['callback'])
            ->getMock()
        ;

        $callback->expects($this->once())
            ->method('callback')
            ->with($this->identicalTo('foo'), $this->identicalTo('bar'))
            ->willReturn(-1)
        ;

        $comparator = new CallbackComparator([$callback, 'callback']);
        $this->assertSame(-1, $comparator->compare('foo', 'bar'));
    }
}
