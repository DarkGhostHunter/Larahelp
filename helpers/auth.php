<?php

use Illuminate\Contracts\Auth\Authenticatable;

if (!function_exists('user')) {
    /**
     * Returns the currently authenticated user, if any.
     *
     * @param  null  $guard
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    function user($guard = null): ?Authenticatable
    {
        return app('auth')->user($guard);
    }
}

if (!function_exists('logged_in')) {
    /**
     * Executes a single callback while the user is logged in.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  callable  $callback
     * @param  string|null  $guard
     *
     * @return mixed
     * @throws \RuntimeException
     */
    function logged_in(Authenticatable $user, callable $callback, string $guard = null): mixed
    {
        $auth = app('auth')->guard($guard);

        if ($auth->user()) {
            throw new RuntimeException("A user [{$auth->user()->getAuthIdentifier()}] was already authenticated");
        }


        $auth->login($user);

        return tap($callback($user), static function () use ($auth): void {
            $auth->logout();
        });
    }
}

