<?php

use Illuminate\Support\Facades\Http;

if (! function_exists('created')) {
    /**
     * Return an HTTP 201 response (OK, Created).
     *
     * @param  mixed  $content
     * @param  int  $code
     * @param  array  $headers
     * @return \Illuminate\Http\Response
     */
    function created($content, $code = 201, $headers = [])
    {
        return response($content, $code, $headers);
    }
}

if (! function_exists('ok')) {
    /**
     * Returns an HTTP 204 response (OK, No Content).
     *
     * @param  array  $headers
     * @return \Illuminate\Http\Response
     */
    function ok($headers = [])
    {
        return response()->noContent(204, $headers);
    }
}

if (! function_exists('routed')) {
    /**
     * Returns the current route of the HTTP Request, or `null` when none.
     *
     * @return \Illuminate\Routing\Route|null
     */
    function routed()
    {
        return app('router')->current();
    }
}

if (! function_exists('routed_is')) {
    /**
     * Determine whether the current route's name matches the given patterns.
     *
     * @param  array  $patterns
     * @return bool
     */
    function routed_is(...$patterns)
    {
        if ($route = routed()) {
            return $route->named(...$patterns);
        }

        return false;
    }
}