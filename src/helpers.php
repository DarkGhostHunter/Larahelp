<?php

use Illuminate\Support\Fluent;
use Illuminate\Cache\RateLimiter;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

if (! function_exists('collect_lazy')) {
    /**
     * Creates a new Lazy Collection.
     *
     * If an integer us used with a callback, it will run the callback nth times.
     *
     * If both arguments are integers, a ranged Lazy Collection will be created.
     *
     * @param  int|callable  $source
     * @param  callable|int|null  $callback
     * @return \Illuminate\Support\LazyCollection
     */
    function collect_lazy($source, $callback = null)
    {
        if (is_int($source)) {
            return is_callable($callback)
                ? LazyCollection::times($source, $callback)
                : LazyCollection::range($source, $callback);
        }

        return new LazyCollection($source);
    }
}

if (! function_exists('collect_times')) {
    /**
     * Create a new collection by invoking the callback a given amount of times.
     *
     * @param  callable  $callback
     * @param  int|null  $times
     * @return \Illuminate\Support\Collection
     */
    function collect_times($times, callable $callback = null)
    {
        return Collection::times($times, $callback);
    }
}

if (! function_exists('data_transform')) {
    /**
     * Transform an item of an array using a callable.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  callable  $callable
     */
    function data_transform(&$target, $key, callable $callable)
    {
        data_set($target, $key, $callable(data_get($target, $key)));
    }
}

if (! function_exists('enclose')) {
    /**
     * Wraps a value into a Closure. It accepts another callable to handle the value.
     *
     * @param  mixed  $value
     * @return \Closure
     */
    function enclose($value)
    {
        return static function (callable $callable = null) use ($value) {
            return $callable ? $callable($value) : $value;
        };
    }
}

if (! function_exists('fluent')) {
    /**
     * Creates a new Fluent instance.
     *
     * @param  array  $attributes
     * @return \Illuminate\Support\Fluent
     */
    function fluent($attributes = [])
    {
        return new Fluent($attributes);
    }
}

if (! function_exists('pipeline')) {
    /**
     * Sends an object through a pipeline.
     *
     * @param  mixed  $passable
     * @param  array  $pipes
     * @param  null  $destination
     * @return mixed
     */
    function pipeline($passable, $pipes, $destination = null)
    {
        $pipeline = app(Pipeline::class)->send($passable)->through($pipes);

        return $destination ? $pipeline->then($destination) : $pipeline->thenReturn();
    }
}

if (! function_exists('throttle')) {
    /**
     * Throttles a given callback by a key. Returns true is the callable is executed.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  int  $tries
     * @param  int  $decayMinutes
     * @return mixed|bool
     */
    function throttle($key, callable $callback, $tries = 60, $decayMinutes = 1)
    {
        $limiter = app(RateLimiter::class);

        if (! $limiter->tooManyAttempts($key, $tries)) {
            $callback();

            $limiter->hit($key, $decayMinutes * 60);

            return true;
        }

        return false;
    }
}

if (! function_exists('unless')) {
    /**
     * Returns a value when a condition is falsy.
     *
     * @param  mixed|bool|\Closure  $condition
     * @param  mixed|\Closure  $value
     * @param  mixed|\Closure|null  $default
     * @return mixed
     */
    function unless($condition, $value, $default = null)
    {
        if (! $result = value($condition)) {
            return $value instanceof Closure ? $value($result) : $value;
        }

        return value($default);
    }
}

if (! function_exists('when')) {
    /**
     * Returns a value when a condition is truthy.
     *
     * @param  mixed|bool|\Closure  $condition
     * @param  mixed|\Closure  $value
     * @param  mixed|\Closure|null  $default
     * @return mixed
     */
    function when($condition, $value, $default = null)
    {
        if ($result = value($condition)) {
            return $value instanceof Closure ? $value($result) : $value;
        }

        return value($default);
    }
}

if (! function_exists('none_of')) {
    /**
     * Checks if the none of the options compared or called returns true.
     *
     * @param  mixed  $subject
     * @param  array|iterable  $options
     * @param  callable|null  $callback
     * @return bool
     */
    function none_of($subject, $options, $callback = null)
    {
        return ! which_of($subject, $options, $callback);
    }
}

if (! function_exists('random_unique')) {
    /**
     * Returns a unique amount of results from a random generator executed a number of times.
     *
     * If $overflow is true, the loop will end only when the results match the number of executions.
     * This can make endless loops, so use with caution around callbacks without enough entropy.
     *
     * @param  int  $times
     * @param  callable  $callback
     * @param  bool  $overflow
     * @return \Illuminate\Support\Collection
     */
    function random_unique($times, $callback, $overflow = false)
    {
        $unique = [];

        beginning:

        $attempts = 0;

        while ($attempts < $times && ! in_array($result = $callback($attempts), $unique, false)) {
            $unique[] = $result;
            $attempts++;
        }

        if ($overflow && count($unique) < $times) {
            goto beginning;
        }

        return collect($unique);
    }
}

if (! function_exists('swap_vars')) {
    /**
     * Swap two variables values, and returns the second argument value.
     *
     * @param  mixed  $swap
     * @param  mixed  $swapped
     * @return mixed
     */
    function swap_vars(&$swap, &$swapped)
    {
        $temp = $swap;
        $swap = $swapped;
        $swapped = $temp;

        unset($temp);

        return $swap;
    }
}

if (! function_exists('which_of')) {
    /**
     * Returns the first option which comparison or callback returns true.
     *
     * @param  mixed  $subject
     * @param  array|iterable  $options
     * @param  callable|null  $callback
     * @return mixed|false
     */
    function which_of($subject, $options, $callback = null)
    {
        $callback = $callback ?? static function ($subject, $option) {
                return $subject === $option;
            };

        foreach ($options as $option) {
            if ($callback($subject, $option)) {
                return $option;
            }
        }

        return false;
    }
}

if (! function_exists('while_sleep')) {
    /**
     * Executes an operation while sleeping milliseconds between multiple executions.
     *
     * @param  int  $times
     * @param  int  $sleep
     * @param  callable  $callback
     * @return \Illuminate\Support\Collection
     */
    function while_sleep($times, $sleep, $callback)
    {
        $sleep *= 1000;

        return collect_times($times, static function ($iteration) use ($callback, $sleep) {
            $result = $callback($iteration);

            usleep($sleep);

            return $result;
        });
    }
}