<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Tests\Equivalence;

use phpmock\phpunit\PHPMock;
use PhpCommon\Comparison\Equivalence\GenericEquivalence;
use PhpCommon\Comparison\Hasher;
use PhpCommon\Comparison\Hasher\GenericHasher;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class GenericEquivalenceTest extends PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var GenericHasher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $equivalence;

    public function setUp()
    {
        $this->equivalence = $this->getMockForAbstractClass(GenericEquivalence::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @testdox The equivalent() method throws an exception if the given value does not match the expected type
     */
    public function testEquivalentThrowsExceptionIfLeftHandValueIsOfAnUnexpectedType()
    {
        $className = GenericEquivalence::class;
        $namespace = substr($className, 0, strrpos($className, '\\'));
        
        $gettype = $this->getFunctionMock($namespace, 'gettype');
        $gettype->expects($this->once())->willReturn('foo');

        $this->equivalence->equivalent(1, 2);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of strings to the equivalentString() method
     */
    public function testHashDelegatesComparisonOfStringsToEquivalentStringMethod()
    {
        $left = 'foo';
        $right = 'bar';

        $this->equivalence->expects($this->once())
            ->method('equivalentString')
            ->with($this->equalTo($left), $this->equalTo($right));

        $this->equivalence->equivalent($left, $right);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of integer numbers to the equivalentInteger() method
     */
    public function testHashDelegatesComparisonOfIntegersToEquivalentIntegerMethod()
    {
        $left = 10;
        $right = 15;

        $this->equivalence->expects($this->once())
            ->method('equivalentInteger')
            ->with($this->equalTo($left), $this->equalTo($right));

        $this->equivalence->equivalent($left, $right);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of floating-point numbers to the equivalentFloat() method
     */
    public function testHashDelegatesComparisonOfFloatsToEquivalentFloatMethod()
    {
        $left = 1.5;
        $right = 2.5;

        $this->equivalence->expects($this->once())
            ->method('equivalentFloat')
            ->with($this->equalTo($left), $this->equalTo($right));

        $this->equivalence->equivalent($left, $right);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of objects to the equivalentObject() method
     */
    public function testHashDelegatesComparisonOfObjectsToEquivalentObjectMethod()
    {
        $left = new stdClass();
        $right = new stdClass();

        $this->equivalence->expects($this->once())
            ->method('equivalentObject')
            ->with($this->equalTo($left), $this->equalTo($right));

        $this->equivalence->equivalent($left, $right);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of resources to the equivalentResource() method
     */
    public function testHashDelegatesComparisonOfResourcesToEquivalentResourceMethod()
    {
        $left = curl_init();
        $right = curl_init();

        $this->equivalence->expects($this->once())
            ->method('equivalentResource')
            ->with($this->equalTo($left), $this->equalTo($right));

        $this->equivalence->equivalent($left, $right);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of boolean values to the equivalentBoolean() method
     */
    public function testHashDelegatesComparisonOfBooleansToEquivalentBooleanMethod()
    {
        $this->equivalence->expects($this->at(0))
            ->method('equivalentBoolean')
            ->with($this->isTrue(), $this->isFalse());

        $this->equivalence->expects($this->at(1))
            ->method('equivalentBoolean')
            ->with($this->isFalse(), $this->isTrue());

        $this->equivalence->equivalent(true, false);
        $this->equivalence->equivalent(false, true);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of NULL values to the equivalentNull() method
     */
    public function testHashDelegatesComparisonOfNullsToEquivalentNullMethod()
    {
        $left = null;
        $right = '';

        $this->equivalence->expects($this->once())
            ->method('equivalentNull');

        $this->equivalence->equivalent($left, $right);
    }

    /**
     * @testdox The equivalent() method delegates the comparison of arrays to the equivalentArray() method
     */
    public function testHashDelegatesComparisonOfArraysToEquivalentArrayMethod()
    {
        $left = [1, 2, 3];
        $right = [3, 2, 1];

        $this->equivalence->expects($this->once())
            ->method('equivalentArray')
            ->with($this->equalTo($left), $this->equalTo($right));

        $this->equivalence->equivalent($left, $right);
    }
}
