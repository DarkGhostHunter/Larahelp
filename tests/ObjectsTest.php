<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Orchestra\Testbench\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class ObjectsTest extends TestCase
{
    public function test_app_call(): void
    {
        $this->app->instance('App\Test', $object = new class {
            public function test(string $string = null): string {
                return $string ?? 'foo';
            }
        });

        static::assertEquals('foo', app_call('App\Test@test'));
        static::assertEquals('bar', app_call('App\Test@test', ['string' => 'bar']));
        static::assertEquals('bar', app_call([$object, 'test'], ['string' => 'bar']));
        static::assertEquals('quz', app_call(static fn($string): string => $string, ['string' => 'quz']));
    }

    public function test_call_existing(): void
    {
        static::assertSame('bar', call_existing(new TestArgumentsOfClass, 'testExisting', 'bar'));
        static::assertFalse(call_existing(new TestArgumentsOfClass, 'bar', 'bar'));

        TestArgumentsOfClass::macro('bar', function ($arguments) {
            return $arguments;
        });

        static::assertSame('bar', call_existing(new TestArgumentsOfClass, 'bar', 'bar'));
    }

    public function test_has_trait(): void
    {
        static::assertTrue(has_trait(TestArgumentsOfClass::class, Macroable::class));
        static::assertFalse(has_trait(TestArgumentsOfClass::class, 'Invalid'));
    }

    public function test_methods_of(): void
    {
        $methods = methods_of(TestArgumentsOfClass::class);

        static::assertInstanceOf(Collection::class, $methods);
        static::assertCount(8, $methods);

        $methods = methods_of(new TestArgumentsOfClass);

        static::assertInstanceOf(Collection::class, $methods);
        static::assertCount(8, $methods);

        $methods = methods_of(TestArgumentsOfClass::class, static function ($method): bool {
            return $method->name === 'test';
        });

        static::assertInstanceOf(Collection::class, $methods);
        static::assertCount(1, $methods);

        $methods = methods_of(TestArgumentsOfClass::class, ReflectionMethod::IS_PROTECTED);

        static::assertInstanceOf(Collection::class, $methods);
        static::assertCount(1, $methods);
    }

    public function test_missing_trait()
    {
        static::assertFalse(missing_trait(TestArgumentsOfClass::class, Macroable::class));
        static::assertTrue(missing_trait(TestArgumentsOfClass::class, 'Invalid'));
    }

    public function test_properties_of(): void
    {
        $class = new class {
            protected $foo;
            public $bar;
            private $quz;
            protected $baz;
        };

        static::assertCount(1, properties_of($class));

        $properties = properties_of($class, static function ($property): bool {
            return $property->name === 'foo';
        });

        static::assertInstanceOf(Collection::class, $properties);
        static::assertCount(1, $properties);

        $properties = properties_of($class, ReflectionProperty::IS_PROTECTED);

        static::assertInstanceOf(Collection::class, $properties);
        static::assertCount(2, $properties);
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
