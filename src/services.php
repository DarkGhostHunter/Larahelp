<?php

use Illuminate\Contracts\Console\Kernel;

if (! function_exists('artisan')) {
    /**
     * Calls an Artisan command, or return the Artisan console instance.
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
     * Hashes a value. If no value is given, the Hasher is returned.
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

if (! function_exists('user')) {
    /**
     * Returns the current user authenticated, or null if is a guest.
     *
     * @param  string|null  $guard
     * @return null|\Illuminate\Contracts\Auth\Authenticatable
     */
    function user($guard = null)
    {
        return app('auth')->guard($guard)->user();
    }
}
