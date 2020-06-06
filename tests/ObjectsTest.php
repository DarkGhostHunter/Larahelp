<?php

namespace Tests;

use ReflectionProperty;
use ReflectionParameter;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Auth\Authenticatable;

class ObjectsTest extends TestCase
{
    public function test_arguments_of()
    {
        $closure = function (int $integer) {
        };

        $arguments = arguments_of($closure);

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(1, $arguments);

        $arguments = arguments_of([TestArgumentsOfClass::class, 'test']);

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(2, $arguments);

        $arguments = arguments_of([new TestArgumentsOfClass, 'test']);

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(2, $arguments);

        $arguments = arguments_of(TestArgumentsOfClass::class, 'test');

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(2, $arguments);

        $arguments = arguments_of(new TestArgumentsOfClass, 'test');

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(2, $arguments);

        $arguments = arguments_of('Tests\TestArgumentsOfClass::test');

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(2, $arguments);

        $arguments = arguments_of('Tests\TestArgumentsOfClass@test');

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(2, $arguments);

        $arguments = arguments_of(new TestArgumentsOfClass);

        $this->assertInstanceOf(Collection::class, $arguments);
        $this->assertInstanceOf(ReflectionParameter::class, $arguments->first());
        $this->assertCount(3, $arguments);
    }

    public function test_call_existing()
    {
        $this->assertSame('bar', call_existing(new TestArgumentsOfClass, 'testExisting', 'bar'));
        $this->assertNull(call_existing(new TestArgumentsOfClass, 'bar', 'bar'));

        TestArgumentsOfClass::macro('bar', function ($arguments) {
            return $arguments;
        });

        $this->assertSame('bar', call_existing(new TestArgumentsOfClass, 'bar', 'bar'));
    }

    public function test_replicate()
    {
        $clone = new class
        {
            public function clone()
            {
                return $this;
            }
        };

        $duplicate = new class
        {
            public function duplicate()
            {
                return 'foo';
            }

            public function clone()
            {
                return $this;
            }

        };

        $replicate = new class
        {
            public function replicate()
            {
                return 'foo';
            }

            public function duplicate()
            {
                return 'foo';
            }

            public function clone()
            {
                return $this;
            }
        };

        $none = new class() {

        };

        $this->assertInstanceOf(get_class($clone), replicate($clone));
        $this->assertInstanceOf(get_class($duplicate), replicate($duplicate));
        $this->assertInstanceOf(get_class($replicate), replicate($replicate));
        $this->assertInstanceOf(get_class($none), replicate($none));
    }

    public function test_has_trait()
    {
        $this->assertTrue(has_trait(TestArgumentsOfClass::class, Macroable::class));
        $this->assertFalse(has_trait(TestArgumentsOfClass::class, 'Invalid'));
    }

    public function test_map_unto()
    {
        $class = new class() extends Fluent {
            public static function make($attributes)
            {
                return new static(['quz' => 'qux']);
            }
        };

        $collection = map_unto([['foo' => 'bar'], ['quz' => 'qux']], Fluent::class);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);

        $collection = map_unto([['foo' => 'bar'], ['quz' => 'qux']], $class, 'make');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertSame('qux', $collection->first()->quz);
        $this->assertSame('qux', $collection->last()->quz);
    }

    public function test_methods_of()
    {
        $methods = methods_of(TestArgumentsOfClass::class);

        $this->assertInstanceOf(Collection::class, $methods);
        $this->assertCount(9, $methods);

        $methods = methods_of(new TestArgumentsOfClass);

        $this->assertInstanceOf(Collection::class, $methods);
        $this->assertCount(9, $methods);

        $methods = methods_of(TestArgumentsOfClass::class, function ($method) {
            return $method->name === 'test';
        });

        $this->assertInstanceOf(Collection::class, $methods);
        $this->assertCount(1, $methods);

        $methods = methods_of(TestArgumentsOfClass::class, \ReflectionMethod::IS_PROTECTED);

        $this->assertInstanceOf(Collection::class, $methods);
        $this->assertCount(1, $methods);
    }

    public function test_missing_trait()
    {
        $this->assertFalse(missing_trait(TestArgumentsOfClass::class, Macroable::class));
        $this->assertTrue(missing_trait(TestArgumentsOfClass::class, 'Invalid'));
    }

    public function test_properties_of()
    {
        $class = new class {
            protected $foo;
            public $bar;
            private $quz;
        };

        $this->assertInstanceOf(Collection::class, properties_of($class));
        $this->assertCount(3, properties_of($class));

        $properties = properties_of($class, function ($property) {
            return $property->name === 'foo';
        });

        $this->assertInstanceOf(Collection::class, $properties);
        $this->assertCount(1, $properties);

        $properties = properties_of($class, ReflectionProperty::IS_PROTECTED);

        $this->assertInstanceOf(Collection::class, $properties);
        $this->assertCount(1, $properties);
    }
}

class TestArgumentsOfClass
{
    use Macroable;

    public function __invoke(string $string, int $int, bool $bool)
    {

    }

    public function test(int $integer, Authenticatable $authenticatable)
    {
        return 'foo';
    }

    public function testExisting($string)
    {
        return $string;
    }

    protected static function invalidMethod()
    {
        return 'quz';
    }
}