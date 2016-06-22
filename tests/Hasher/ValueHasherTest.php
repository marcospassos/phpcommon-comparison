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

use PHPUnit_Framework_TestCase;
use PhpCommon\Comparison\Equatable;
use PhpCommon\Comparison\Equivalence;
use PhpCommon\Comparison\Hasher\ValueHasher;
use PhpCommon\Comparison\Hashable;
use PhpCommon\Comparison\Hasher;
use PhpCommon\Comparison\Tests\Fixtures\CustomDate;
use PhpCommon\Comparison\Tests\Fixtures\User;
use stdClass;
use DateTime;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class ValueHasherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ValueHasher
     */
    protected $hasher;

    public function setUp()
    {
        $this->hasher = new ValueHasher();
    }

    /**
     * @return array[]
     */
    public function getEqualInstances()
    {
        $mock = $this->createMock(Equivalence::class);
        $mock->method('equals')
            ->will($this->returnValue(true));

        $custom = ['DateTime' => $mock];

        return [
            [$equivalence = new ValueHasher(), $equivalence],
            [new ValueHasher(), new ValueHasher()],
            [$customized = new ValueHasher($custom), $customized],
            [new ValueHasher($custom), new ValueHasher($custom)]
        ];
    }

    /**
     * @param Equatable $left
     * @param Equatable $right
     *
     * @dataProvider getEqualInstances
     *
     * @testdox The equals() method returns true if instances are of the same class and are equally configured
     */
    public function testEqualsReturnsTrueIfInstancesAreOfSameClassAndEquallyConfigured(
        Equatable $left,
        Equatable $right
    ) {
        $this->assertTrue($left->equals($right));
    }

    /**
     * @return array[]
     */
    public function getUnequalInstances()
    {
        $external = $this->createMock(ValueHasher::class);
        $external->method('equals')
            ->will($this->returnValue(false));

        $mapping1 = ['DateTime' => $external];
        $mapping2 = ['DateTime2' => $external];

        $subclass = $this->createMock(ValueHasher::class);

        return [
            [new ValueHasher(), $subclass],
            [new ValueHasher($mapping1), new ValueHasher()],
            [new ValueHasher($mapping1), new ValueHasher($mapping1)],
            [new ValueHasher($mapping1), new ValueHasher($mapping2)],
        ];
    }

    /**
     * @param Equatable $left
     * @param Equatable $right
     *
     * @dataProvider getUnequalInstances
     *
     * @testdox The equals() method returns false if instances are of different classes or are not equally configured
     */
    public function testEqualsReturnsFalseIfInstancesAreOfDifferentClassesOrNotEquallyConfigured(
        Equatable $left,
        Equatable $right
    ) {
        $this->assertFalse($left->equals($right));
    }

    /**
     * @return array[]
     */
    public function getInstancesForTransitivityTests()
    {
        $external = $this->createMock(Equivalence::class);
        $external->method('equals')
            ->will($this->returnValue(true));

        $mapping = ['DateTime' => $external];

        return [
            [$default = new ValueHasher(), clone $default, clone $default],
            [$nonDefault = new ValueHasher($mapping), clone $nonDefault, clone $nonDefault],
        ];
    }

    /**
     * @param Equatable $a
     * @param Equatable $b
     * @param Equatable $c
     *
     * @dataProvider getInstancesForTransitivityTests
     *
     * @testdox The equals() method is transitive
     */
    public function testEqualsIsTransitive(Equatable $a, Equatable $b, Equatable $c)
    {
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($c));
        $this->assertTrue($a->equals($c));
    }

    /**
     * @return array[]
     */
    public function getEquivalentValues()
    {
        return [
            [$object = new stdClass(), $object],
            [$resource = curl_init(), $resource],
            [new User(1), new User(1)],
            [[1, null, true], [1, null, true]],
            [[], []],
            [[$object, 2], [$object, 2]],
            [[1, 2, new User(3)], [1, 2, new User(3)]],
        ];
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @dataProvider getEquivalentValues
     *
     * @testdox The equivalent() method returns true if values are considered equivalent
     */
    public function testEquivalentReturnsTrueIfValuesAreEqual($left, $right)
    {
        $this->assertTrue($this->hasher->equivalent($left, $right));
    }

    /**
     * @return array[]
     */
    public function getNonEquivalentValues()
    {
        return [
            [true, new stdClass()],
            [new stdClass(), new stdClass()],
            [new User(1), new User(2)],
            [new User(1), true],
            [curl_init(), curl_init()],
            [[1, null, true], [1, true, null]],
            [[1, null], [1]],
            [[new stdClass()], [new stdClass()]],
            [[1], [true]],
            [[0], [false]],
            [[0], [null]],
            [[false], [null]],
            [[true], [new stdClass()]],
            [[], [1]],
            [[1, null, 2, ], ['a' => 1, 'b' => null, 'c' => 2]],
        ];
    }

    /**
     * @param mixed $left
     * @param mixed $right
     *
     * @dataProvider getNonEquivalentValues
     *
     * @testdox The equivalent() method returns false if values are not considered equivalent
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
            [new User(1), new User(1), new User(1)],
            [$user = new User(1), $user, new User(1)],
            [[1, 2], [1, 2], [1, 2]],
            [[], [], []]
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
            [new User(1)],
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
     * @testdox The equivalent() delegates the comparison between Equatable objects to the objects being compared
     */
    public function testEquivalentDelegatesComparisonToEquatableObjects()
    {
        $object = $this->createMock(Equatable::class);

        $equatable = $this->createMock(Equatable::class);
        $equatable->expects($this->once())
            ->method('equals')
            ->with($this->equalTo($object))
            ->will($this->returnValue(true));

        $this->hasher->equivalent($equatable, $object);
    }

    /**
     * @testdox The equivalent() returns false if Equatable objects are of different types
     */
    public function testEquivalentReturnsFalseIfEquatableObjectsAreOfDifferentTypes()
    {
        $object = new stdClass();

        $equatable = $this->createMock(Equatable::class);
        $equatable->expects($this->never())
            ->method('equals');

        $this->assertFalse($this->hasher->equivalent($equatable, $object));
    }

    /**
     * @testdox The equivalent() compares objects for identity by default
     */
    public function testEquivalentComparesObjectsForIdentityByDefault()
    {
        $left = new stdClass();
        $right = new stdClass();

        $this->assertTrue($this->hasher->equivalent($left, $left));
        $this->assertFalse($this->hasher->equivalent($left, $right));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @testdox The equivalent() throws an exception if an object is Equatable but not Hashable
     */
    public function testHashThrowsAnExceptionIfAnObjectIsEquatableButNotHashable()
    {
        $object = $this->createMock(Equatable::class);
        $this->hasher->hash($object);
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

    /**
     * @testdox Allows an external Equivalence to be specified for comparing objects of a particular class
     */
    public function testEquivalentDelegatesComparisonToExternalEquivalence()
    {
        $left = new DateTime();
        $right = new stdClass();

        $custom = $this->createMock(Equivalence::class);
        $custom->expects($this->exactly(4))
            ->method('equivalent')
            ->with(
                $this->equalTo($left),
                $this->equalTo($right)
            )
            ->will($this->onConsecutiveCalls(true, true, false, false));

        $equivalence = new ValueHasher(['DateTime' => $custom]);

        $this->assertTrue($equivalence->equivalent($left, $right));
        $this->assertTrue($equivalence->equivalent($right, $left));

        $this->assertFalse($equivalence->equivalent($left, $right));
        $this->assertFalse($equivalence->equivalent($right, $left));
    }

    /**
     * @testdox Ensures that an external Equivalence specified for a class also applies to its subclasses
     */
    public function testEquivalentDelegatesComparisonToExternalEquivalenceSpecifiedForAParentClass()
    {
        $left = new CustomDate();
        $right = clone $left;

        $custom = $this->createMock(Equivalence::class);
        $custom->expects($this->once())
            ->method('equivalent')
            ->with($this->equalTo($right))
            ->will($this->returnValue(true));

        $equivalence = new ValueHasher(['DateTime' => $custom]);

        $this->assertTrue($equivalence->equivalent($left, $right));
    }

    /**
     * @testdox Allows an external Hasher to be specified for hashing objects of a particular class
     */
    public function testHashDelegatesHashingToExternalHasher()
    {
        $object = $this->createMock(Hashable::class);
        $object->expects($this->exactly(2))
            ->method('getHash')
            ->will($this->returnValue(123));

        $this->assertSame(
            $this->hasher->hash($object),
            $this->hasher->hash($object)
        );
    }

    /**
     * @testdox Ensures that an external Hasher specified for a class also applies to its subclasses
     */
    public function testHashDelegatesHashingToExternalHasherSpecifiedForAParentClass()
    {
        $date = new CustomDate();

        $custom = $this->createMock(Hasher::class);
        $custom->expects($this->exactly(2))
            ->method('hash')
            ->with($this->equalTo($date))
            ->will($this->returnValue(123));

        $equivalence = new ValueHasher(['DateTime' => $custom]);
        $this->assertSame($equivalence->hash($date), $equivalence->hash($date));
    }
}
