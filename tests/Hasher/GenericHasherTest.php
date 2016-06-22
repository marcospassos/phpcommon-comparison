<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Tests\Hasher;

use PhpCommon\Comparison\Hasher;
use PhpCommon\Comparison\Hasher\GenericHasher;
use phpmock\phpunit\PHPMock;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class GenericHasherTest extends PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var GenericHasher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $hasher;

    public function setUp()
    {
        $this->hasher = $this->getMockForAbstractClass(GenericHasher::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @testdox The hash() method throws an exception if the given value does not match the expected type
     */
    public function testHashThrowsExceptionIfValueIsOfAnUnexpectedType()
    {
        $className = GenericHasher::class;
        $namespace = substr($className, 0, strrpos($className, '\\'));

        $gettype = $this->getFunctionMock($namespace, 'gettype');
        $gettype->expects($this->once())->willReturn('foo');

        $this->hasher->hash(1);
    }

    /**
     * @testdox The hash() method delegates hashing of strings to the hashString() method
     */
    public function testHashDelegatesHashingOfStringsToHashStringMethod()
    {
        $value = 'foo';

        $this->hasher->expects($this->once())
            ->method('hashString')
            ->with($this->equalTo($value));

        $this->hasher->hash($value);
    }

    /**
     * @testdox The hash() method delegates hashing of integer numbers to the hashInteger() method
     */
    public function testHashDelegatesHashingOfIntegersToHashIntegerMethod()
    {
        $value = 10;

        $this->hasher->expects($this->once())
            ->method('hashInteger')
            ->with($this->equalTo($value));

        $this->hasher->hash($value);
    }

    /**
     * @testdox The hash() method delegates hashing of floating-point numbers to the hashFloat() method
     */
    public function testHashDelegatesHashingOfFloatsToHashFloatMethod()
    {
        $value = 1.5;

        $this->hasher->expects($this->once())
            ->method('hashFloat')
            ->with($this->equalTo($value));

        $this->hasher->hash($value);
    }

    /**
     * @testdox The hash() method delegates hashing of objects to the hashObject() method
     */
    public function testHashDelegatesHashingOfObjectsToHashObjectMethod()
    {
        $value = new stdClass();

        $this->hasher->expects($this->once())
            ->method('hashObject')
            ->with($this->equalTo($value));

        $this->hasher->hash($value);
    }

    /**
     * @testdox The hash() method delegates hashing of resources to the hashResource() method
     */
    public function testHashDelegatesHashingOfResourcesToHashResourceMethod()
    {
        $value = curl_init();

        $this->hasher->expects($this->once())
            ->method('hashResource')
            ->with($this->equalTo($value));

        $this->hasher->hash($value);
    }

    /**
     * @testdox The hash() method delegates hashing of boolean values to the hashBoolean() method
     */
    public function testHashDelegatesHashingOfBooleansToHashBooleanMethod()
    {
        $this->hasher->expects($this->at(0))
            ->method('hashBoolean')
            ->with($this->equalTo(true));

        $this->hasher->expects($this->at(1))
            ->method('hashBoolean')
            ->with($this->equalTo(false));

        $this->hasher->hash(true);
        $this->hasher->hash(false);
    }

    /**
     * @testdox The hash() method delegates hashing of NULL values to the hashNull() method
     */
    public function testHashDelegatesHashingOfNullsToHashNullMethod()
    {
        $value = null;

        $this->hasher->expects($this->once())
            ->method('hashNull');

        $this->hasher->hash($value);
    }

    /**
     * @testdox The hash() method delegates hashing of strings to the hashString() method
     */
    public function testHashDelegatesHashingOfArraysToHashArrayMethod()
    {
        $value = [1, 2, 3];

        $this->hasher->expects($this->once())
            ->method('hashArray')
            ->with($this->equalTo($value));

        $this->hasher->hash($value);
    }
}
