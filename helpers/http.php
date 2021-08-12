<?php

use Illuminate\Http\Response;

if (!function_exists('created')) {
    /**
     * Return an HTTP 201 response (OK, Created).
     *
     * @param  mixed  $content
     * @param  int  $code
     * @param  array  $headers
     *
     * @return \Illuminate\Http\Response
     */
    function created(mixed $content = '', int $code = 201, array $headers = []): Response
    {
        return response($content, $code, $headers);
    }
}

if (!function_exists('ok')) {
    /**
     * Returns an HTTP 204 response (OK, No Content).
     *
     * @param  array  $headers
     *
     * @return \Illuminate\Http\Response
     */
    function ok(array $headers = []): Response
    {
        return response()->noContent(204, $headers);
    }
}

if (!function_exists('route_is')) {
    /**
     * Determine whether the current route's name matches the given patterns.
     *
     * @param  string  ...$patterns
     *
     * @return bool  It will always return `false` when outside an HTTP Request.
     */
    function route_is(string ...$patterns): bool
    {
        return (bool) app('request')->routeIs(...$patterns);
    }
}
