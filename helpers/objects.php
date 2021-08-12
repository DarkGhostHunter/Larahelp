<?php

use Illuminate\Support\Collection;

if (!function_exists('app_call')) {
    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback  The callable or a callable string separated with `@`.
     * @param  mixed  ...$parameters
     *
     * @return mixed
     */
    function app_call(callable|string $callback, array $parameters = []): mixed
    {
        return app()->call($callback, $parameters);
    }
}

if (!function_exists('call_existing')) {
    /**
     * Calls a dynamic method or macro if it exists in the object instance.
     *
     * @param  object|string  $object  $object
     * @param  string  $method
     * @param  mixed  ...$parameters
     *
     * @return mixed  Returns `false` if the call wasn't executed.
     */
    function call_existing(object|string $object, string $method, mixed ...$parameters): mixed
    {
        if (method_exists($object, $method) || (method_exists($object, 'hasMacro') && $object::hasMacro($method))) {
            return call_user_func_array([$object, $method], $parameters);
        }

        return false;
    }
}

if (!function_exists('has_trait')) {
    /**
     * Checks recursively if the object is using a single trait.
     *
     * @param  object|string  $object
     * @param  string  $trait
     *
     * @return bool
     */
    function has_trait(object|string $object, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($object), true);
    }
}

if (!function_exists('methods_of')) {
    /**
     * Returns a collection of all public methods from a given class or object.
     *
     * @param  object|string  $object
     * @param  \Closure|int  $filter
     *
     * @return \Illuminate\Support\Collection<\ReflectionMethod>|\ReflectionMethod[]
     */
    function methods_of(object|string $object, Closure|int $filter = ReflectionMethod::IS_PUBLIC): Collection
    {
        $reflection = new ReflectionClass($object);

        $collection = $filter instanceof Closure
            ? Collection::make($reflection->getMethods())->filter($filter)
            : Collection::make($reflection->getMethods($filter));

        return $collection->keyBy(static function (ReflectionMethod $method): string {
            return $method->name;
        });
    }
}

if (!function_exists('missing_trait')) {
    /**
     * Checks recursively if the object is not using a trait.
     *
     * @param  object|string  $object
     * @param  string  $trait
     *
     * @return bool
     */
    function missing_trait(object|string $object, string $trait): bool
    {
        return !has_trait($object, $trait);
    }
}

if (!function_exists('properties_of')) {
    /**
     * Returns a collection of all public properties from a given class or object.
     *
     * @param  object|string  $object
     * @param  \Closure|int  $filter
     *
     * @return \Illuminate\Support\Collection
     */
    function properties_of(object|string $object, Closure|int $filter = ReflectionProperty::IS_PUBLIC): Collection
    {
        $reflection = new ReflectionClass($object);

        $collection = $filter instanceof Closure
            ? Collection::make($reflection->getProperties())->filter($filter)
            : Collection::make($reflection->getProperties($filter));

        return $collection->keyBy(static function (ReflectionProperty $property): string {
            return $property->name;
        });
    }
}
