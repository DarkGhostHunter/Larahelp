<?php

use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

if (!function_exists('diff')) {
    /**
     * Returns the difference between two dates in seconds or any other given unit.
     *
     * @param  \DateTimeInterface|string|int  $from
     * @param  \DateTimeInterface|string|int|null  $until
     * @param  string  $in
     * @param  bool  $absolute  When `false`, the difference will become negative if `$until` is less than `$from`.
     *
     * @return int|float
     */
    function diff(
        DateTimeInterface|string|int $from,
        DateTimeInterface|string|int $until = null,
        string $in = 'seconds',
        bool $absolute = true,
    ): int|float
    {
        return until($from, $until, $absolute)->total($in);
    }
}

if (!function_exists('weekstart')) {
    /**
     * Returns the start of the week.
     *
     * @param  \DateTimeInterface|string|int  $from
     * @param  int  $weekStartAt
     *
     * @return \Illuminate\Support\Carbon
     */
    function weekstart(DateTimeInterface|string|int $from = 'now', int $weekStartAt = 0): Carbon
    {
        return Carbon::parse($from)->startOfWeek($weekStartAt);
    }
}

if (!function_exists('weekend')) {
    /**
     * Returns the end of the week.
     *
     * @param  \DateTimeInterface|string|int  $from
     * @param  int  $weekStartAt
     *
     * @return \Illuminate\Support\Carbon
     */
    function weekend(DateTimeInterface|string|int $from = 'now', int $weekStartAt = 0): Carbon
    {
        return Carbon::parse($from)->endOfWeek($weekStartAt);
    }
}

if (!function_exists('until')) {
    /**
     * Returns the interval from a date until the desired date.
     *
     * @param  \DateTimeInterface|string|int  $from
     * @param  \DateTimeInterface|string|int  $until
     * @param  bool  $absolute
     *
     * @return \Carbon\CarbonInterval
     */
    function until(
        DateTimeInterface|string|int $from,
        DateTimeInterface|string|int $until,
        bool $absolute = true
    ): CarbonInterval
    {
        return Carbon::parse($from)->diffAsCarbonInterval($until, $absolute);
    }
}

if (! function_exists('period')) {
    /**
     * Returns the period of a given start and end or interval.
     *
     * @param  \DateTimeInterface|string|int  $start
     * @param  \DateTimeInterface|\DateInterval|string|int  $end
     * @param  \DateInterval|string|int|null  $interval
     * @param  string|null  $unit  If specified, $interval must be an integer
     *
     * @return \Carbon\CarbonPeriod
     */
    function period(
        DateTimeInterface|string|int $start,
        DateTimeInterface|DateInterval|string|int $end,
        DateInterval|string|int $interval = null,
        string $unit = null
    ): CarbonPeriod
    {
        return Carbon::parse($start)->toPeriod($end, $interval, $unit);
    }
}

if (!function_exists('period_from')) {
    /**
     * Returns the next or previous period from given date, exclusive.
     *
     * @param  \DatePeriod|\Carbon\CarbonPeriod  $periods  If it's an Interval, it will be a single period.
     * @param  \DateTimeInterface|string|int  $at
     * @param  bool  $after  When `true`, it will check if it's after or equal the date.
     * @param  bool  $inclusive  When `true`, dates equal to the compared datatime will be considered.
     *
     * @return \Illuminate\Support\Carbon|null  Returns `null` if the next period is outside `$at`.
     */
    function period_from(
        DatePeriod|CarbonPeriod $periods,
        DateTimeInterface|string|int $at = 'now',
        bool $after = true,
        bool $inclusive = false,
    ): ?Carbon
    {
        $periods = CarbonPeriod::instance($periods);

        $at = Carbon::parse($at);

        $call = $after ? 'greaterThan' : 'lessThan';

        if ($inclusive) {
            $call .= 'OrEqualTo';
        }

        return $after
            ? Collection::make($periods)->first->{$call}($at)
            : Collection::make($periods)->reverse()->first->{$call}($at);
    }
}

if (!function_exists('yesterday')) {
    /**
     * Returns the date for yesterday.
     *
     * @param  \DateTimeZone|string|null  $tz
     *
     * @return \Illuminate\Support\Carbon
     */
    function yesterday(DateTimeZone|string $tz = null): Carbon
    {
        return Carbon::yesterday($tz);
    }
}
