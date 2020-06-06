<?php

if (! function_exists('arguments_of')) {
    /**
     * Returns a collection of arguments from a callable.
     *
     * @param  string|callable|object  $callback
     * @param  string|null  $method
     * @return \Illuminate\Support\Collection|\ReflectionParameter[]
     * @throws \ReflectionException
     */
    function arguments_of($callback, $method = null)
    {
        if ($callback instanceof Closure) {
            $reflection = new ReflectionFunction($callback);
        }
        elseif (is_array($callback)) {
            $reflection = new ReflectionMethod($callback[0], $callback[1]);
        }
        elseif ($method) {
            $reflection = new ReflectionMethod($callback, $method);
        }
        elseif (is_string($callback)) {
            if (strpos($callback, '::') !== false) {
                $callback = explode('::', $callback, 2);
            }
            else {
                $callback = explode('@', $callback, 2);
            }

            $reflection = new ReflectionMethod($callback[0], $callback[1]);
        }
        else {
            $reflection = new ReflectionMethod($callback, '__invoke');
        }

        return collect($reflection->getParameters());
    }
}

if (! function_exists('call_existing')) {
    /**
     * Calls a dynamic method or macro if it exists in the object instance.
     *
     * @param  object  $object
     * @param  string  $method
     * @param  mixed  ...$parameters
     * @return mixed|void
     */
    function call_existing($object, $method, ...$parameters)
    {
        if (method_exists($object, $method)
            || (method_exists($object, 'hasMacro') && $object::hasMacro($method))) {
            return $object->{$method}(...$parameters);
        }
    }
}

if (! function_exists('replicate')) {
    /**
     * Replicates an object.
     *
     * It priorices "clone", "duplicate" and "replicate" methods before native cloning.
     *
     * @param  object  $object
     * @return object
     */
    function replicate(object $object)
    {
        $method = which_of($object, ['clone', 'duplicate', 'replicate'], static function ($subject, $method) {
            return method_exists($subject, $method);
        });

        if ($method) {
            return $object->{$method}();
        }

        return clone $object;
    }
}

if (! function_exists('has_trait')) {
    /**
     * Checks recursively if the object is using a single trait.
     *
     * @param  object|string  $object
     * @param  string  $trait
     * @return bool
     */
    function has_trait($object, $trait)
    {
        return in_array($trait, class_uses_recursive($object), true);
    }
}

if (! function_exists('map_unto')) {
    /**
     * Instance items into objects passing the item as constructor or static method call parameter.
     *
     * @param  mixed  $items
     * @param  string  $class
     * @param  string  $call
     * @return \Illuminate\Support\Collection|mixed[]
     */
    function map_unto($items, $class, $call = null)
    {
        $collection = collect($items);

        if (! $call) {
            return $collection->mapInto($class);
        }

        return $collection->map(static function ($item, $key) use ($class, $call) {
            return $class::{$call}($item, $key);
        });
    }
}

if (! function_exists('methods_of')) {
    /**
     * Returns a collection of all methods from a given class or object.
     *
     * @param  string|object  $object
     * @param  int|callable|null  $filter
     * @return \Illuminate\Support\Collection|\ReflectionMethod[]
     * @throws \ReflectionException
     */
    function methods_of($object, $filter = null)
    {
        $reflection = new ReflectionClass($object);

        return is_callable($filter)
            ? collect($reflection->getMethods())->filter($filter)
            : collect($reflection->getMethods($filter));
    }
}

if (! function_exists('missing_trait')) {
    /**
     * Checks recursively if the object is not using a trait.
     *
     * @param  object|string  $object
     * @param  string  $trait
     * @return bool
     */
    function missing_trait($object, $trait)
    {
        return ! has_trait($object, $trait);
    }
}

if (! function_exists('properties_of')) {
    /**
     * Returns a collection of all properties from a given class or object.
     *
     * @param  string|object  $object
     * @param  int|callable|null  $filter
     * @return \Illuminate\Support\Collection|\ReflectionProperty[]
     * @throws \ReflectionException
     */
    function properties_of($object, $filter = null)
    {
        $reflection = new ReflectionClass($object);

        return is_callable($filter)
            ? collect($reflection->getProperties())->filter($filter)
            : collect($reflection->getProperties($filter));
    }
}