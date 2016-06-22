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

use PHPUnit_Framework_TestCase;
use PhpCommon\Comparison\Hasher\IdentityHasher;
use stdClass;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class IdentityHasherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IdentityHasher
     */
    protected $hasher;

    public function setUp()
    {
        $this->hasher = new IdentityHasher();
    }

    /**
     * @testdox The equals() method returns true if instances are of the same class
     */
    public function testEqualsReturnTrueIfInstancesAreOfSameClass()
    {
        $other = new IdentityHasher();

        $this->assertTrue($this->hasher->equals($this->hasher));
        $this->assertTrue($this->hasher->equals($other));
        $this->assertTrue($other->equals($this->hasher));
    }

    /**
     * @testdox The equals() method returns false if instances are of different classes
     */
    public function testEqualsReturnFalseIfInstancesAreOfDifferentClasses()
    {
        /** @var IdentityHasher $mock */
        $mock = $this->createMock(IdentityHasher::class);

        $this->assertFalse($this->hasher->equals($mock));
    }

    /**
     * @return array[]
     */
    public function getEquivalentValues()
    {
        return [
            [10, 10],
            [0, 0],
            [1.7, 1.7],
            [INF, INF],
            [-INF, -INF],
            ['abc', 'abc'],
            ['', ''],
            [true, true],
            [false, false],
            [null, null],
            [$object = new stdClass(), $object],
            [$resource = curl_init(), $resource],
            [[], []],
            [[1, null, true], [1, null, true]]
        ];
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @dataProvider getEquivalentValues
     *
     * @testdox The equivalent() method returns true if values are identical
     */
    public function testEquivalentReturnsTrueIfValuesAreIdentical($left, $right)
    {
        $this->assertTrue($this->hasher->equivalent($left, $right));
    }

    /**
     * @return array[]
     */
    public function getNonEquivalentValues()
    {
        return [
            [10, 11],
            [INF, -INF],
            [NAN, NAN],
            [0, 0.0],
            [1.7, 1.71],
            ['abc', 'ab'],
            ['', 0],
            [false, true],
            [true, 1],
            [false, 0],
            [null, 0],
            [null, false],
            [true, new stdClass()],
            [$object = new stdClass(), clone $object],
            [curl_init(), curl_init()],
            [[1, null, true], [1, true, null]],
            [[1, null], [1]],
            [[1], [true]],
            [[0], [false]],
            [[0], [null]],
            [[false], [null]],
            [[true], [new stdClass()]],
        ];
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @dataProvider getNonEquivalentValues
     *
     * @testdox The equivalent() method returns false if values are not identical
     */
    public function testEquivalentReturnsFalseIfValuesAreNotIdentical($left, $right)
    {
        $this->assertFalse($this->hasher->equivalent($left, $right));
    }

    /**
     * @return array[]
     */
    public function getValuesForTransitivityTests()
    {
        return [
            [10, 10, 10],
            ['abc', 'abc', 'abc'],
            ['', '', ''],
            [true, true, true],
            [false, false, false],
            [null, null, null],
            [$object = new stdClass(), $object, $object],
            [$resource = curl_init(), $resource, $resource],
            [[1, null, true], [1, null, true], [1, null, true]]
        ];
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     *
     * @dataProvider getValuesForTransitivityTests
     *
     *  @testdox The equivalent() method is transitive
     */
    public function testEquivalentIsTransitive($a, $b, $c)
    {
        $this->assertTrue($this->hasher->equivalent($a, $b));
        $this->assertTrue($this->hasher->equivalent($b, $c));
        $this->assertTrue($this->hasher->equivalent($a, $c));
    }

    /**
     * @return array[]
     */
    public function getValuesForReflexivityTests()
    {
        return [
            [10],
            [1.7],
            ['abc'],
            [''],
            [true],
            [false],
            [null],
            [new stdClass()],
            [curl_init()],
            [[]],
            [[1, null, true], [1, null, true]]
        ];
    }

    /**
     * @param mixed $value
     *
     * @dataProvider getValuesForReflexivityTests
     *
     *  @testdox The equivalent() method is reflexive
     */
    public function testEquivalentIsReflexive($value)
    {
        $this->assertTrue($this->hasher->equivalent($value, $value));
    }

    /**
     * @return array[]
     */
    public function getValuesForSymmetryTests()
    {
        return array_merge(
            $this->getEquivalentValues(),
            $this->getNonEquivalentValues()
        );
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @dataProvider getValuesForSymmetryTests
     *
     *  @testdox The equivalent() method is symmetric
     */
    public function testEquivalentIsSymmetric($left, $right)
    {
        $this->assertSame(
            $this->hasher->equivalent($left, $right),
            $this->hasher->equivalent($right, $left)
        );
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @dataProvider getEquivalentValues
     *
     * @testdox The equivalent() produces the same hash-code for equivalent values
     */
    public function testHashIsConsistentWithEquivalent($left, $right)
    {
        $this->assertTrue($this->hasher->equivalent($left, $right));

        $this->assertSame(
            $this->hasher->hash($left),
            $this->hasher->hash($right)
        );
    }
}
