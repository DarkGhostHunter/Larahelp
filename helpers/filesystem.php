<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

if (!function_exists('dot_path')) {
    /**
     * Transforms a relative path into dot notation.
     *
     * @param  string  $path
     *
     * @return string
     */
    function dot_path(string $path): string
    {
        return (string) Str::of($path)->replace(['\\', '/'], '.')->trim('.');
    }
}

if (!function_exists('files')) {
    /**
     * Returns the local Filesystem helper, or a list of files in a path.
     *
     * @param  string|null  $path
     * @param  bool  $recursive
     *
     * @return \Illuminate\Filesystem\Filesystem|\Illuminate\Support\Collection<\SplFileInfo>|\SplFileInfo[]
     */
    function files(string $path = null, bool $recursive = false): Filesystem|Collection
    {
        $filesystem = app('files');

        if (! $path) {
            return $filesystem;
        }

        $path = base_path($path);

        return Collection::make($recursive ? $filesystem->allFiles($path) : $filesystem->files($path));
    }
}

if (!function_exists('undot_path')) {
    /**
     * Transforms a path from dot notation to a relative path.
     *
     * @param  string  $dotPath
     *
     * @return string
     */
    function undot_path(string $dotPath): string
    {
        return (string) Str::of($dotPath)->replace('.', DIRECTORY_SEPARATOR)->trim(DIRECTORY_SEPARATOR);
    }
}
