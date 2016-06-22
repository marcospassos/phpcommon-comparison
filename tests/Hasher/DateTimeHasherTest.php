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
use PhpCommon\Comparison\Hasher\DateTimeHasher;
use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;
use DateTimeInterface;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class DateTimeHasherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeHasher
     */
    protected $hasher;

    public function setUp()
    {
        $this->hasher = new DateTimeHasher();
    }

    /**
     * @testdox The equals() method returns true if instances are of the same class
     */
    public function testEqualsReturnTrueIfInstancesAreOfSameClass()
    {
        $other = new DateTimeHasher();

        $this->assertTrue($this->hasher->equals($this->hasher));
        $this->assertTrue($this->hasher->equals($other));
        $this->assertTrue($other->equals($this->hasher));
    }

    /**
     * @testdox The equals() method returns false if instances are of different classes
     */
    public function testEqualsReturnFalseIfInstancesAreOfDifferentClasses()
    {
        /** @var DateTimeHasher $mock */
        $mock = $this->createMock(DateTimeHasher::class);

        $this->assertFalse($this->hasher->equals($mock));
    }

    /**
     * @return array[]
     */
    public function getEquivalentDates()
    {
        return [
            [
                DateTime::createFromFormat('Y-m-d', '2016-01-01'),
                DateTime::createFromFormat('Y-m-d', '2016-01-01'),
            ],
            [
                DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 00:00:01'),
                DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 00:00:01'),
            ],
            [
                new DateTime('@1171502725'),
                new DateTime('@1171502725')
            ],
            [
                new DateTimeImmutable('@1171502726'),
                new DateTimeImmutable('@1171502726')
            ],
            [
                new DateTime('@1171502725'),
                new DateTimeImmutable('@1171502725'),
            ]
        ];
    }

    /**
     * @param DateTimeInterface $left
     * @param DateTimeInterface $right
     *
     * @dataProvider getEquivalentDates
     *
     * @testdox The equivalent() method returns true if DateTime instances have the same date, time and timezone
     */
    public function testEquivalentReturnsTrueIfInstancesHaveSameDateTimeAndTimeZone(
        DateTimeInterface $left,
        DateTimeInterface $right
    ) {
        $this->assertTrue($this->hasher->equivalent($left, $right));
    }

    /**
     * @return array[]
     */
    public function getNonEquivalentDates()
    {
        return [
            [
                DateTime::createFromFormat('Y-m-d', '2016-01-01'),
                DateTime::createFromFormat('Y-m-d', '2016-01-02'),
            ],

            [
                DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 00:00:00'),
                DateTime::createFromFormat('Y-m-d H:i:s', '2016-01-01 00:00:01'),
            ],

            [
                new DateTime('@1171502725'),
                new DateTime('@1171502726')
            ],

            [
                DateTime::createFromFormat(
                    'Y-m-d',
                    '2016-05-01',
                    new DateTimeZone('America/Sao_Paulo')
                ),
                DateTime::createFromFormat(
                    'Y-m-d',
                    '2016-05-01',
                    new DateTimeZone('America/Argentina/Buenos_Aires')
                )
            ],

            [
                new DateTime('@1171502725'),
                new DateTimeImmutable('@1171502726')
            ],

            [
                new DateTime('@1171502725'),
                '1171502725'
            ]
        ];
    }

    /**
     * @param DateTimeInterface $left
     * @param mixed $right
     *
     * @dataProvider getNonEquivalentDates
     *
     * @testdox The equivalent() method returns false if DateTime instances have different date, time or timezone
     */
    public function testEquivalentReturnsFalseIfInstancesHaveDifferentDateTimeOrTimeZone(
        DateTimeInterface $left,
        $right
    ) {
        $this->assertFalse($this->hasher->equivalent($left, $right));
    }

    /**
     * @expectedException \PhpCommon\Comparison\UnexpectedTypeException
     *
     * @testdox The equivalent() method throws an exception if the left-hand value does not implement DateTimeInterface
     */
    public function testEquivalentThrowsExceptionIfNotADateTimeInstance()
    {
        $this->hasher->equivalent('a', 'b');
    }

    /**
     * @param DateTimeInterface $left
     * @param DateTimeInterface $right
     *
     * @dataProvider getEquivalentDates
     *
     * @testdox The equivalent() produces the same hash-code for equivalent dates
     */
    public function testHashIsConsistentWithEquivalent(DateTimeInterface $left, DateTimeInterface $right)
    {
        $this->assertTrue($this->hasher->equivalent($left, $right));

        $this->assertSame(
            $this->hasher->hash($left),
            $this->hasher->hash($right)
        );
    }
}
