<?php

use Illuminate\Support\Str;

if (! function_exists('base_path_of')) {
    /**
     * Return the relative path of a class from the project root path.
     *
     * @param  string|object  $object
     * @return string
     * @throws \ReflectionException
     */
    function base_path_of($object)
    {
        $path = class_defined_at($object, false);

        return substr($path, strlen(base_path()) + 1);
    }
}

if (! function_exists('class_defined_at')) {
    /**
     * Returns where the file path where the object was defined.
     *
     * @param  string|object  $object
     * @param  bool  $withLine
     * @return string
     * @throws \ReflectionException
     */
    function class_defined_at($object, $withLine = true)
    {
        $reflection = new ReflectionClass($object);

        return $reflection->getFileName() . ($withLine ? ':' . $reflection->getStartLine() : '');
    }
}

if (! function_exists('dot_path')) {
    /**
     * Returns a relative path in dot notation.
     *
     * @param  string  $path
     * @return string
     */
    function dot_path($path)
    {
        return Str::of($path)
            ->replace(['\\', '/'], '.')
            ->start('.')
            ->substr(1)
            ->__toString();
    }
}

if (! function_exists('undot_path')) {
    /**
     * Returns a relative path from a dot notation string.
     *
     * @param  string  $path
     * @return string
     */
    function undot_path($path)
    {
        return Str::of($path)
            ->replace('.', DIRECTORY_SEPARATOR)
            ->start(DIRECTORY_SEPARATOR)
            ->substr(1)
            ->__toString();
    }
}