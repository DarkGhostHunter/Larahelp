<?php

namespace Tests;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Orchestra\Testbench\TestCase;

class DatetimeTest extends TestCase
{
    public function test_diff(): void
    {
        $this->travelTo(Carbon::create(2015, 7, 4, 20));

        static::assertSame(60 * 60 * 24 * 10, diff(today(), today()->addDays(10)));
        static::assertSame(10, diff(today(), today()->addDays(10), 'days'));
        static::assertSame(10, diff(today(), today()->subDays(10), 'days'));
        static::assertSame(-10, diff(today(), today()->subDays(10), 'days', false));
    }

    public function test_weekend()
    {
        $this->travelTo(Carbon::create(2015, 7, 4, 20));

        static::assertSame('2015-07-05 23:59:59', weekend()->toDateTimeString());
        static::assertSame('2015-07-06 23:59:59', weekend(weekStartAt: 1)->toDateTimeString());

        static::assertSame('2015-07-12 23:59:59', weekend('2015-07-06 23:59:59')->toDateTimeString());
        static::assertSame('2015-07-06 23:59:59', weekend('2015-07-06 23:59:59', 1)->toDateTimeString());
    }

    public function test_weekstart(): void
    {
        $this->travelTo(Carbon::create(2015, 7, 4, 20));

        static::assertSame('2015-06-28 00:00:00', weekstart()->toDateTimeString());
        static::assertSame('2015-06-29 00:00:00', weekstart(weekStartAt: 1)->toDateTimeString());

        static::assertSame('2015-06-28 00:00:00', weekstart('2015-06-29 00:00:00')->toDateTimeString());
        static::assertSame('2015-06-29 00:00:00', weekstart('2015-06-29 00:00:00', 1)->toDateTimeString());
    }

    public function test_until(): void
    {
        $this->travelTo(Date::create(2020, 01, 01, 02, 30));

        static::assertSame(60 * 60 * 24 * 10, until(today(), today()->addDays(10))->total('seconds'));
        static::assertSame(10, until(today(), today()->addDays(10))->total('days'));
        static::assertSame(10, until(today(), today()->subDays(10))->total('days'));
        static::assertSame(-10, until(today(), today()->subDays(10), false)->total('days'));
    }

    public function test_period()
    {
        Date::setTestNow(Date::create(2020, 01, 01, 16, 30));

        $period = period('2018-04-20', '2018-04-25');
        static::assertInstanceOf(CarbonPeriod::class, $period);
        static::assertSame('2018-04-20', $period->getStartDate()->toDateString());
        static::assertSame('2018-04-20T00:00:00+00:00/P1D/2018-04-25T00:00:00+00:00', $period->spec());

        $period = period('2018-04-21', '3 days');
        static::assertInstanceOf(CarbonPeriod::class, $period);
        static::assertSame('2018-04-21', $period->getStartDate()->toDateString());
        static::assertSame('2018-04-21T00:00:00+00:00/P1D/2020-01-04T16:30:00+00:00', $period->spec());

        $period = period('2018-04-21', '2018-04-27', '3 days');
        static::assertInstanceOf(CarbonPeriod::class, $period);
        static::assertSame('2018-04-21', $period->getStartDate()->toDateString());
        static::assertSame('2018-04-27', $period->getEndDate()->toDateString());
        static::assertSame('2018-04-21T00:00:00+00:00/P3D/2018-04-27T00:00:00+00:00', $period->spec());

        $period = period('2018-04-21', '2018-04-27', 3, 'day');
        static::assertInstanceOf(CarbonPeriod::class, $period);
        static::assertSame('2018-04-21', $period->getStartDate()->toDateString());
        static::assertSame('2018-04-27', $period->getEndDate()->toDateString());
        static::assertSame('2018-04-21T00:00:00+00:00/P3D/2018-04-27T00:00:00+00:00', $period->spec());
    }

    public function test_period_from(): void
    {
        $this->travelTo(Carbon::create(2018, 1, 5));

        $period = period('2018-01-01', '2018-01-10', 3, 'day');

        static::assertNull(period_from($period, '2018-01-10 00:00:01'));
        static::assertNull(period_from($period, '2018-01-10 00:00:00'));
        static::assertSame(
            '2018-01-04 00:00:00',
            period_from($period, '2018-01-04 00:00:00', inclusive: true)->toDateTimeString()
        );
        static::assertSame('2018-01-01 00:00:00', period_from($period, '2017-12-31 23:59:59')->toDateTimeString());
        static::assertSame('2018-01-07 00:00:00', period_from($period)->toDateTimeString());
        static::assertSame('2018-01-04 00:00:00', period_from($period, '2018-01-02')->toDateTimeString());

        static::assertNull(period_from($period, '2017-12-31 23:59:59', false));
        static::assertNull(period_from($period, '2018-01-01 00:00:00', false));
        static::assertSame(
            '2018-01-07 00:00:00',
            period_from($period, '2018-01-07 00:00:00', false, true)->toDateTimeString()
        );
        static::assertSame('2018-01-10 00:00:00', period_from($period, '2018-01-10 00:00:01', false)->toDateTimeString());
        static::assertSame('2018-01-04 00:00:00', period_from($period, after: false)->toDateTimeString());
        static::assertSame('2018-01-01 00:00:00', period_from($period, '2018-01-02', false)->toDateTimeString());
    }

    public function test_yesterday()
    {
        $this->travelTo(Date::create(2020, 01, 01, 02, 30));

        $yesterday = yesterday();
        static::assertSame('2019-12-31 00:00:00', $yesterday->toDateTimeString());

        $yesterday = yesterday('America/New_York');
        static::assertSame('2019-12-30 00:00:00', $yesterday->toDateTimeString());
    }
}
