<?php

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable as LaravelStringable;

if (!function_exists('data_update')) {
    /**
     * Updates an item of an array or object using a callback that receives it.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  callable<mixed>  $callable
     *
     * @return mixed
     */
    function data_update(mixed &$target, string|array $key, callable $callable): mixed
    {
        return data_set($target, $key, $callable(data_get($target, $key)));
    }
}

if (!function_exists('delist')) {
    /**
     * Returns the values of the array, so these can be listed into variables.
     *
     * @param  array  $items
     * @param  int  $offset
     *
     * @return array
     */
    function delist(array $items, int $offset = 0): array
    {
        return array_slice(array_values($items), $offset);
    }
}

if (!function_exists('enclose')) {
    /**
     * Wraps a value or callable into a Closure, if it's not already callable.
     *
     * @param  mixed  $value
     *
     * @return \Closure
     */
    function enclose(mixed $value): Closure
    {
        if ($value instanceof Closure) {
            return $value;
        }

        if (is_callable($value)) {
            return Closure::fromCallable($value);
        }

        return static function (mixed ...$params) use ($value) {
            return value($value, ...$params);
        };
    }
}

if (!function_exists('in_console')) {
    /**
     * Check if the application is running in console.
     *
     * @param  callable|null  $callback  If added, it will be run if the application is in console.
     *
     * @return bool
     */
    function in_console(callable $callback = null): bool
    {
        if (app()->runningInConsole()) {
            value($callback);
            return true;
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }
}

if (!function_exists('in_development')) {
    /**
     * Check if the application is running in development environments (no testing, staging or production).
     *
     * @param  callable|null  $callback
     *
     * @return bool
     */
    function in_development(callable $callback = null): bool
    {
        if (app()->environment('dev', 'development', 'local')) {
            value($callback);
            return true;
        }

        return false;
    }
}

if (!function_exists('none_of')) {
    /**
     * Checks if none of the options compared to a subject, or called with it, returns something truthy.
     *
     * @param  mixed  $subject
     * @param  iterable  $options
     * @param  callable|null  $callback
     *
     * @return bool
     */
    function none_of(mixed $subject, iterable $options, callable $callback = null): bool
    {
        return false === which_of($subject, $options, $callback);
    }
}

if (!function_exists('pipe')) {
    /**
     * Sends an object through a pipeline.
     *
     * @param  mixed  $passable
     * @param  array|object[]|callable[]|string[]|class-string[]  $pipes
     * @param  callable|null  $destination
     *
     * @return mixed
     */
    function pipe(mixed $passable, array $pipes, callable $destination = null): mixed
    {
        $destination ??= static function (mixed $result): mixed {
            return $result;
        };

        return app(Pipeline::class)->send($passable)->through($pipes)->then($destination);
    }
}

if (!function_exists('remember')) {
    /**
     * Retrieves an item from the cache, or stores a default value if the item doesn't exist.
     *
     * @param  string  $key
     * @param  \Closure|\DateTimeInterface|\DateInterval|int  $ttl
     * @param  \Closure|\DateTimeInterface|\DateInterval|int|null  $callback
     * @param  int|null  $lock  If issued, it will lock the key and wait the same amount of seconds.
     *
     * @return mixed
     */
    function remember(
        string $key,
        Closure|DateTimeInterface|DateInterval|int $ttl,
        Closure|DateTimeInterface|DateInterval|int $callback = null,
        int $lock = null
    ): mixed
    {
        $cache = app('cache');

        if (is_callable($ttl)) {
            [$ttl, $callback, $lock] = [null, $ttl, $callback];
        }

        if ($lock) {
            return $cache->lock($key, $lock)->block($ttl, static fn() => $cache->remember($key, $ttl, $callback));
        }

        return $cache->remember($key, $ttl, $callback);
    }
}

if (!function_exists('sleep_between')) {
    /**
     * Runs a callback while sleeping between multiple executions.
     *
     * It returns a collection of each callback result.
     *
     * @param  int  $times
     * @param  int  $sleep  Milliseconds to sleep. 1000 equals to 1 second.
     * @param  callable  $callback
     *
     * @return \Illuminate\Support\Collection
     */
    function sleep_between(int $times, int $sleep, callable $callback): Collection
    {
        $sleep *= 1000;

        return Collection::times($times, static function ($iteration) use ($callback, $sleep, $times): mixed {
            $result = $callback($iteration);

            if ($iteration < $times) {
                usleep($sleep);
            }

            return $result;
        });
    }
}

if (!function_exists('taptap')) {
    /**
     * Call the given Closure with the given value then return the value, twice.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    function taptap(mixed $value, callable $callback = null): mixed
    {
        return tap(tap($value, $callback));
    }
}

if (!function_exists('hashy')) {
    /**
     * Creates a small BASE64 encoded MD5 hash from a string for portable checksum.
     *
     * @param  \Stringable|\Illuminate\Support\Stringable|string  $hashable
     * @param  string|null  $hash  The hash to compare the result.
     *
     * @return string|bool  Returns a boolean if a comparable hash has been set to compare.
     */
    function hashy(Stringable|LaravelStringable|string $hashable, string $hash = null): string|bool
    {
        $hashed = base64_encode(md5((string)$hashable, true));

        return $hash ? hash_equals($hash, $hashed) : $hashed;
    }
}

if (!function_exists('which_of')) {
    /**
     * Returns the key of the option which comparison or callback returns true.
     *
     * If the callback returns something truthy, that value will be used.
     *
     * @param  mixed  $subject
     * @param  iterable  $options
     * @param  callable|null  $callback
     *
     * @return mixed  If no results returns truthy, `false` will be returned.
     */
    function which_of(mixed $subject, iterable $options, callable $callback = null): mixed
    {
        $callback = $callback ?? static function (mixed $subject, mixed $option): bool {
            return $subject === $option;
        };

        foreach ($options as $key => $option) {
            if ($result = $callback($subject, $option)) {
                return $result === true ? $key : $result;
            }
        }

        return false;
    }
}
