<?php

namespace Tests;

use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Date;
use Orchestra\Testbench\TestCase;

class DatetimeTest extends TestCase
{
    public function test_diff()
    {
        Date::setTestNow();

        $today = diff('yesterday');

        $this->assertInstanceOf(CarbonInterval::class, $today);
        $this->assertSame(0, $today->invert);
        $this->assertSame(1, $today->floorDays()->totalDays);

        $today = diff(now()->subDay(), now()->subDays(10));

        $this->assertInstanceOf(CarbonInterval::class, $today);
        $this->assertSame(0, $today->invert);
        $this->assertSame(9, $today->roundDays()->totalDays);

        $today = diff(now()->subDay(), now()->subDays(10), false);

        $this->assertInstanceOf(CarbonInterval::class, $today);
        $this->assertSame(1, $today->invert);
        $this->assertSame(-9, $today->roundDays()->totalDays);
    }

    public function test_period()
    {
        Date::setTestNow(Date::create(2020, 01, 01, 16, 30));

        $period = period('2018-04-20', '2018-04-25');
        $this->assertInstanceOf(CarbonPeriod::class, $period);
        $this->assertSame('2018-04-20', $period->getStartDate()->toDateString());
        $this->assertSame('2018-04-20T00:00:00+00:00/P1D/2018-04-25T00:00:00+00:00', $period->spec());

        $period = period('2018-04-21', '3 days');
        $this->assertInstanceOf(CarbonPeriod::class, $period);
        $this->assertSame('2018-04-21', $period->getStartDate()->toDateString());
        $this->assertSame('2018-04-21T00:00:00+00:00/P1D/2020-01-04T16:30:00+00:00', $period->spec());

        $period = period('2018-04-21', '3 days', '2018-04-27');
        $this->assertInstanceOf(CarbonPeriod::class, $period);
        $this->assertSame('2018-04-21', $period->getStartDate()->toDateString());
        $this->assertSame('2018-04-27', $period->getEndDate()->toDateString());
        $this->assertSame('2018-04-21T00:00:00+00:00/P3D/2018-04-27T00:00:00+00:00', $period->spec());

        $period = period('2018-04-21', 3, '2018-04-27', 'day');
        $this->assertInstanceOf(CarbonPeriod::class, $period);
        $this->assertSame('2018-04-21', $period->getStartDate()->toDateString());
        $this->assertSame('2018-04-27', $period->getEndDate()->toDateString());
        $this->assertSame('2018-04-21T00:00:00+00:00/P3D/2018-04-27T00:00:00+00:00', $period->spec());
    }

    public function test_from_now()
    {
        Date::setTestNow(Date::create(2020, 01, 01, 16, 30)->setTimezone('UTC'));

        $from_now = from_now('4 days');
        $this->assertSame('2020-01-05 16:30:00', $from_now->toDateTimeString());

        $from_now = from_now('day', 4);
        $this->assertSame('2020-01-05 16:30:00', $from_now->toDateTimeString());

        $from_now = from_now('month', 60, 'America/New_York');
        $this->assertSame('2025-01-01 11:30:00', $from_now->toDateTimeString());
    }

    public function test_until_now()
    {
        Date::setTestNow(Date::create(2020, 01, 01, 16, 30)->setTimezone('UTC'));

        $until_now = until_now('4 days');
        $this->assertSame('2019-12-28 16:30:00', $until_now->toDateTimeString());

        $until_now = until_now('day', 4);
        $this->assertSame('2019-12-28 16:30:00', $until_now->toDateTimeString());

        $until_now = until_now('month', 60, 'America/New_York');
        $this->assertSame('2015-01-01 11:30:00', $until_now->toDateTimeString());
    }

    public function test_yesterday()
    {
        Date::setTestNow(Date::create(2020, 01, 01, 02, 30)->setTimezone('UTC'));

        $yesterday = yesterday();
        $this->assertSame('2019-12-31 00:00:00', $yesterday->toDateTimeString());

        $yesterday = yesterday('America/New_York');
        $this->assertSame('2019-12-30 00:00:00', $yesterday->toDateTimeString());
    }
}
