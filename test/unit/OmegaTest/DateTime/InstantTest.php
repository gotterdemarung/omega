<?php

namespace OmegaTest\Type;

use Omega\DateTime\Instant;
use OmegaTest\Test;

class InstantTest extends Test
{
    protected function setUp()
    {
        date_default_timezone_set('Europe/Kiev');
    }

    protected function assertConstruct($expect, $argument, $precision = null)
    {
        $x = new Instant($argument, $precision);
        $this->assertSame($expect, $x->getFloat());
    }

    public function testConstructor()
    {
        // Empty value testing
        $this->assertConstruct((float) 0, null);
        $this->assertConstruct((float) 0, false);
        $this->assertConstruct((float) 0, '');
        $this->assertConstruct((float) 0, 0);

        // Raw value
        $this->assertConstruct((float) 12345, 12345); // int
        $this->assertConstruct(12345.678, 12345.678); // float
        $this->assertConstruct(12345.01, (double) 12345.01); // double

        // String timestamp
        $this->assertConstruct(12345.678, '12345.678');
        $this->assertConstruct((float) 12345, '12345');

        // PHP Datetime
        $this->assertConstruct((float) 1382263994, new \DateTime('2013-10-20 12:13:14', new \DateTimeZone('Europe/Paris')));
        $this->assertConstruct((float) 1382220000, new \DateTime('2013-10-20', new \DateTimeZone('Europe/Paris')));
        $this->assertConstruct((float) 1382216400, new \DateTime('2013-10-20', new \DateTimeZone('Europe/Kiev')));
        $this->assertConstruct((float) 1382227200, new \DateTime('2013-10-20', new \DateTimeZone('UTC')));

        // Self
        $this->assertConstruct(12.34, new Instant(12.34));

        // String to time
        $this->assertConstruct((float) 944165504, '1999-12-02 22:11:44');
    }

    public function testEquals()
    {
        $x = new Instant(12.34);
        $this->assertTrue($x->equals(12.34));
        $this->assertTrue($x->equals(new Instant('12.34')));
        $this->assertFalse($x->equals(12.3456));
        $this->assertFalse($x->equals(12));
    }

    public function testFormat()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame('1973-11-30 00:33:09', $x->format('Y-m-d H:i:s'));
        $this->assertSame('30.11.73', $x->format('d.m.y'));
    }

    public function testDayBegin()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame(123454800, $x->getDayBegin()->getTimestamp());
    }

    public function testDayEnd()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame(123541199, $x->getDayEnd()->getTimestamp());
    }

    public function testGetFloat()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame(123456789.76, $x->getFloat());
    }

    public function testGetInt()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame(123456789, $x->getTimestamp());
    }

    public function testGetMySQLDate()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame('1973-11-30', $x->getMySQLDate());
    }

    public function testGetMySQLDateTime()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame('1973-11-30 00:33:09', $x->getMySQLDateTime());
    }

    public function testGetUnixTimestamp()
    {
        $x = new Instant(123456789.76, 2);
        $this->assertSame(123456789, $x->getTimestamp());
    }

    public function testBiggerThan()
    {
        $x = new Instant(12.76345);
        $this->assertTrue($x->isBiggerThen(10));
        $this->assertFalse($x->isBiggerThen(13));
    }

    public function testLesserThan()
    {
        $x = new Instant(12.76345);
        $this->assertTrue($x->isLesserThen(20));
        $this->assertFalse($x->isLesserThen(10));
    }

    public function testStaticNow()
    {
        $start = microtime(true);
        $x = Instant::now()->getFloat();
        $this->assertGreaterThanOrEqual($start, $x);
        $this->assertLessThanOrEqual(microtime(true), $x);
    }

    public function testStaticValid()
    {
        $this->assertTrue(Instant::isValidStringTimeStamp('12345'));
        $this->assertTrue(Instant::isValidStringTimeStamp('12345.33'));
        $this->assertTrue(Instant::isValidStringTimeStamp('-12345'));

        $this->assertFalse(Instant::isValidStringTimeStamp('12345,33'));
        $this->assertFalse(Instant::isValidStringTimeStamp(PHP_INT_MAX . '1'));
    }

    public function testStaticMkTime()
    {
        $this->assertSame(1146711721, Instant::create(3, 2, 1, 5, 4, 6)->getTimestamp());
    }

}