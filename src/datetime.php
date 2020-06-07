<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

if (! function_exists('diff')) {
    /**
     * Returns the difference between two dates.
     *
     * @param  \Carbon\CarbonInterface|\DateTimeInterface|string|null  $start
     * @param  \Carbon\CarbonInterface|\DateTimeInterface|string|null  $end
     * @param  bool  $absolute
     * @return \Carbon\CarbonInterval|\DateInterval
     */
    function diff($start, $end = null, $absolute = true)
    {
        return Date::parse($start)->diffAsCarbonInterval($end, $absolute);
    }
}

if (! function_exists('period')) {
    /**
     * Returns the period of a given start and end or interval.
     *
     * @param  \DateTimeInterface|Carbon|CarbonImmutable|int|null  $start
     * @param  \DateTimeInterface|Carbon|CarbonImmutable|int|null  $interval
     * @param  \DateTimeInterface|Carbon|CarbonImmutable|int|null  $end
     * @param  string|null  $unit  if specified, $interval must be an integer
     * @return \Carbon\CarbonPeriod|\DatePeriod
     */
    function period($start = null, $interval = null, $end = null, $unit = null)
    {
        if (! $end) {
            swap_vars($end, $interval);
        }

        return Date::parse($start)->toPeriod($end, $interval, $unit);
    }
}

if (! function_exists('from_now')) {
    /**
     * Creates a datetime with an interval of time from now.
     *
     * @param  string|\DateInterval  $unit
     * @param  int  $value
     * @param  string|null  $tz
     * @return \Illuminate\Support\Carbon
     */
    function from_now($unit, $value = null, $tz = null)
    {
        $now = now($tz);

        if (! $value) {
            return $now->add($unit);
        }

        return $now->addUnit($unit, $value);
    }
}

if (! function_exists('until_now')) {
    /**
     * Creates a datetime with an interval of time until now.
     *
     * @param  string|\DateInterval  $unit
     * @param  int|null  $value
     * @param  string|null  $tz
     * @return \Illuminate\Support\Carbon
     */
    function until_now($unit, $value = null, $tz = null)
    {
        $now = now($tz);

        if (! $value) {
            return $now->sub($unit);
        }

        return $now->subUnit($unit, $value);
    }
}

if (! function_exists('yesterday')) {
    /**
     * Create a new Carbon instance for yesterday.
     *
     * @param  \DateTimeZone|string|null  $tz
     * @return \Illuminate\Support\Carbon
     */
    function yesterday($tz = null)
    {
        return Date::yesterday($tz);
    }
}