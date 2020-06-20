<?php

use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Console\Kernel;

if (! function_exists('artisan')) {
    /**
     * Returns the Artisan console instance, or calls an Artisan command.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface|null  $outputBuffer
     * @return int|Illuminate\Contracts\Console\Kernel
     */
    function artisan($command = null, $parameters = [], $outputBuffer = null)
    {
        $artisan = app(Kernel::class);

        return $command ? $artisan->call($command, $parameters, $outputBuffer) : $artisan;
    }
}

if (! function_exists('hasher')) {
    /**
     * Returns the Hasher instance, or hashes a value.
     *
     * @param  string|null  $value
     * @param  array  $options
     * @return \Illuminate\Contracts\Hashing\Hasher|string
     */
    function hasher($value = null, $options = [])
    {
        $hasher = app('hash');

        return $value ? $hasher->make($value, $options) : $hasher;
    }
}

if (! function_exists('http')) {
    /**
     * Returns a Pending Request, or sends a Request and returns a Response.
     *
     * @param  string  $verb
     * @param  string  $url
     * @param  array|string|null  $data
     * @return \Illuminate\Http\Client\PendingRequest|\Illuminate\Http\Client\Response
     */
    function http($verb = null, $url = null, $data = null)
    {
        $request = Http::asJson();

        return $verb ? $request->{strtolower($verb)}($url, $data) : $request;
    }
}

if (!function_exists('rate_limiter')) {
    /**
     * Return a new the Rate Limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    function rate_limiter()
    {
        return app(RateLimiter::class);
    }
}

if (! function_exists('user')) {
    /**
     * Returns the current user authenticated, or `null` if is a guest.
     *
     * @param  string|null  $guard
     * @return null|\Illuminate\Contracts\Auth\Authenticatable
     */
    function user($guard = null)
    {
        return app('auth')->guard($guard)->user();
    }
}
