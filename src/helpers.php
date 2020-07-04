<?php

use Illuminate\Support\Fluent;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

if (! function_exists('callable_with')) {
    /**
     * Returns a callable that calls another callable with parameters.
     *
     * @param  callable  $callable
     * @param  array  $parameters
     * @return \Closure
     */
    function callable_with($callable, ...$parameters)
    {
        return static function () use ($callable, $parameters) {
            return $callable(...$parameters);
        };
    }
}

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

if (! function_exists('collect_map')) {
    /**
     * Creates a new collection after passing each item through a callback.
     *
     * @param  mixed  $items
     * @param  callable  $callback
     * @return \Illuminate\Support\Collection
     */
    function collect_map($items, $callback)
    {
        $keys = array_keys($items);

        return collect(array_combine($keys, array_map($callback, $items, $keys)));
    }
}

if (! function_exists('collect_times')) {
    /**
     * Create a new collection by invoking a callback a given amount of times.
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
     * Transform an item of an array or object using a callback.
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
     * Wraps a value into a Closure. It accepts another callback to handle the value.
     *
     * @param  mixed  $value
     * @return \Closure
     */
    function enclose($value)
    {
        if ($value instanceof Closure) {
            return $value;
        }

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
     * Returns the Rate Limiter or throttles a given callback by a key.
     *
     * If only a key is given, it will return when it will be available.
     *
     * It will return `true` if the callback is executed, and `false` when not.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  int  $tries
     * @param  int  $decayMinutes
     * @return \Illuminate\Cache\RateLimiter|\Illuminate\Support\Carbon|bool
     */
    function throttle($key = null, callable $callback = null, $tries = 60, $decayMinutes = 1)
    {
        $limiter = rate_limiter();

        if (0 === $args = func_num_args()) {
            return $limiter;
        }

        if ($args === 1) {
            return now()->addSeconds($limiter->availableIn($key));
        }

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
     * Checks if none of the options compared or called to a subject returns true.
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

if (! function_exists('random_bool')) {
    /**
     * Returns a random boolean value.
     *
     * If the seed is zero, it will always return `false`.
     *
     * If the seed is negative, odds will favor `false`.
     *
     * @param  int  $seed
     * @return bool
     */
    function random_bool($seed = 1)
    {
        if ($seed < 0) {
            return (bool)random_int($seed, 1);
        }

        return (bool)random_int(0, $seed);
    }
}

if (! function_exists('random_unique')) {
    /**
     * Returns a unique amount of results from a random generator executed a number of times.
     *
     * If `$overflow` is true, the loop will end only when the results match the number of
     * executions, which may create endless loops. Use with caution around callbacks that
     * don't have enough entropy to return unique results, like 10 times on `rand(1,2)`.
     *
     * @param  int  $times
     * @param  callable  $callback
     * @param  bool  $overflow
     * @return \Illuminate\Support\Collection|mixed[]
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
     * Swap two variables values, and returns the second variable original value.
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

if (! function_exists('list_from')) {
    /**
     * Returns the values of the array, so these can be listed into variables.
     *
     * @param  array  $items
     * @param  int  $offset
     * @return array
     */
    function list_from($items, $offset = 0)
    {
        return array_slice(array_values($items), $offset);
    }
}

if (! function_exists('which_of')) {
    /**
     * Returns the first option which comparison or callback returns true.
     *
     * If no results returns truthy, `false` will be returned.
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
     * Runs a callback while sleeping between multiple executions.
     *
     * It returns a collection of each callback result.
     *
     * @param  int  $times
     * @param  int  $sleep  Milliseconds to sleep. 1000 equals to 1 second.
     * @param  callable  $callback
     * @return \Illuminate\Support\Collection
     */
    function while_sleep($times, $sleep, $callback)
    {
        $sleep *= 1000;

        return collect_times($times, static function ($iteration) use ($callback, $sleep, $times) {
            $result = $callback($iteration);

            if ($iteration < $times) {
                usleep($sleep);
            }

            return $result;
        });
    }
}