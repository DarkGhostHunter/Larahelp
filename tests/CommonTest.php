<?php

namespace Tests;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Stringable;

class CommonTest extends TestCase
{
    public function test_data_update(): void
    {
        $array = [
            'foo' => [
                'bar', 'quz' => [
                    'qux' => 'quuz',
                ],
            ],
        ];

        data_update($array, 'foo.quz.qux', static function ($value): string {
            return $value . '.quux';
        });

        static::assertSame($array['foo']['quz']['qux'], 'quuz.quux');

        data_update($array, 'foo.quz.qux', static function ($value): void {
            $value . '.quux';
        });

        static::assertNull($array['foo']['quz']['qux']);
    }

    public function test_remember(): void
    {
        $result = remember('foo', static function():string {
            return 'bar';
        });

        static::assertSame('bar', $result);

        Cache::forget('foo');

        $result = remember('foo', 10, static function(): string {
            return 'bar';
        });

        static::assertSame('bar', $result);

        Cache::put('foo', 'baz');

        $result = remember('foo', 10, static function(): string {
            return 'bar';
        });

        static::assertSame('baz', $result);

        Cache::forget('foo');
        Cache::lock('foo', 2);

        $result = remember('foo', 10, static function (): string {
            return 'bar';
        }, 10);

        static::assertSame('bar', $result);

        static::assertFalse(Cache::lock('foo', 2)->release());

        Cache::forget('foo');
        Cache::lock('foo', 2);

        $result = remember('foo', static function (): string {
            return 'bar';
        }, 10);

        static::assertSame('bar', $result);

        static::assertFalse(Cache::lock('foo', 2)->release());
    }

    public function test_delist(): void
    {
        $list = ['foo' => 'bar', 'quz' => 'qux', 'quuz' => 'quux'];

        [$foo, $quz, $quuz] = delist($list);

        static::assertSame('bar', $foo);
        static::assertSame('qux', $quz);
        static::assertSame('quux', $quuz);

        [$fox, $dog] = delist($list, 1);

        static::assertSame('qux', $fox);
        static::assertSame('quux', $dog);
    }

    public function test_enclose(): void
    {
        $object = new class() {
            public function test(): string
            {
                return 'foo';
            }
        };

        $enclose = enclose([$object, 'test']);

        static::assertInstanceOf(Closure::class, $enclose);
        static::assertSame('foo', $enclose());

        $enclose = enclose('foo');

        static::assertInstanceOf(Closure::class, $enclose);
        static::assertSame('foo', $enclose());

        $enclose = enclose(static function (string $something): string {
            return $something . '.quz';
        });

        static::assertInstanceOf(Closure::class, $enclose);
        static::assertSame('bar.quz', $enclose('bar'));
    }

    public function test_in_console(): void
    {
        static::assertTrue(in_console());

        $executed = false;

        in_console(static function() use (&$executed): void {
            $executed = true;
        });

        static::assertTrue($executed);
    }

    public function test_in_development(): void
    {
        $original = $this->app['env'];

        $this->app['env'] = 'dev';
        static::assertTrue(in_development());

        $executed = false;

        in_development(static function() use (&$executed): void {
            $executed = true;
        });

        static::assertTrue($executed);

        $this->app['env'] = 'development';
        static::assertTrue(in_development());

        $this->app['env'] = 'local';
        static::assertTrue(in_development());

        $this->app['env'] = 'testing';
        static::assertFalse(in_development());

        $this->app['env'] = 'production';
        static::assertFalse(in_development());

        $this->app['env'] = 'staging';
        static::assertFalse(in_development());

        $this->app['env'] = 'stage';
        static::assertFalse(in_development());

        $this->app['env'] = $original;
    }

    public function test_none_of(): void
    {
        static::assertTrue(none_of('foo', ['bar', 'quz', 'qux']));
        static::assertFalse(none_of('foo', ['bar', 'quz', 'qux', 'foo']));

        static::assertTrue(none_of(5, [2, 7, 13], static function ($subject, $option): bool {
            return $option % $subject === 0;
        }));
        static::assertFalse(none_of(5, [2, 7, 15], static function ($subject, $option): bool {
            return $option % $subject === 0;
        }));
    }

    public function test_pipe(): void
    {
        $barToQuz = new class {
            public function handle($bar, $next)
            {
                $bar = $bar === 'bar' ? 'quz' : $bar;

                return $next($bar);
            }
        };

        $pipe = pipe('foo', [
            static function (string $foo, Closure $next): string {
                $foo = $foo === 'foo' ? 'bar' : $foo;

                return $next($foo);
            },
            $barToQuz,
        ]);

        static::assertSame('quz', $pipe);

        $pipe = pipe('foo', [
            static function (string $foo, Closure $next): string {
                $foo = $foo === 'foo' ? 'bar' : $foo;

                return $next($foo);
            },
            $barToQuz,
        ], static function (string $result): string {
            return $result === 'quz' ? 'qux' : $result;
        });

        static::assertSame('qux', $pipe);
    }

    public function test_while_sleep(): void
    {
        $start = time();
        $collection = sleep_between(4, 1000, static function (): string {
            return microtime(false);
        });
        $end = time();

        static::assertCount(4, $collection);
        static::assertGreaterThanOrEqual(3, $end - $start);
        static::assertLessThan(5, $end - $start);
    }

    public function test_shadow(): void
    {
        $object = new Fluent([
            'foo' => 'bar'
        ]);

        shadow($object, true)->offsetSet('foo', 'quz');

        static::assertSame('quz', $object->foo);

        shadow($object, false)->offsetSet('foo', 'bar');

        static::assertSame('quz', $object->foo);

        $object = new Fluent([
            'foo' => 'bar'
        ]);

        shadow($object, static function ($object): bool {
            return $object->foo === 'bar';
        })->offsetSet('foo', 'baz');

        static::assertSame('baz', $object->foo);

        shadow($object, static function ($object): bool {
            return $object->foo === 'bar';
        })->offsetSet('foo', 'bar');

        static::assertSame('baz', $object->foo);
    }

    public function test_taptap(): void
    {
        $object = new class {
            public bool $called = false;
            public function call(): bool {
                return true;
            }
        };

        static::assertInstanceOf(HigherOrderTapProxy::class, taptap($object));
        static::assertInstanceOf(HigherOrderTapProxy::class, taptap($object)->call());
        static::assertSame($object, taptap($object)->call()->call());
        static::assertTrue(taptap($object)->call()->call()->call());

        taptap($object, static function ($instance): void {
            $instance->called = true;
        });

        static::assertTrue($object->called);
    }

    public function test_hashy(): void
    {
        $hashable = 'This is a hashable string';
        $expected = 'TJYa8+63dRbdN6w44shX1g==';

        static::assertSame($expected, hashy($hashable));
        static::assertSame($expected, hashy(Str::of($hashable)));
        static::assertSame($expected, hashy(new class implements Stringable {
            public function __toString(): string
            {
                return 'This is a hashable string';
            }
        }));

        static::assertTrue(hashy($hashable, $expected));
        static::assertFalse(hashy($hashable, $expected . '='));
        static::assertFalse(hashy($hashable, 'TJYa8+63dRbdN6w44shX1g='));
    }

    public function test_which_of(): void
    {
        static::assertSame(3, which_of('foo', ['bar', 'quz', 'qux', 'foo']));
        static::assertFalse(which_of('foo', ['bar', 'quz', 'qux']));

        static::assertSame('baz', which_of(5, ['foo' => 2, 'bar' => 7, 'baz' => 15], static function ($subject, $option): bool {
            return $option % $subject === 0;
        }));

        static::assertFalse(which_of(5, [2, 7, 13], static function ($subject, $option): bool {
            return $option % $subject === 0;
        }));

        static::assertSame('cougar', which_of(5, ['foo' => 2, 'bar' => 7, 'baz' => 15], static function ($subject, $option) {
            if ($option % $subject === 0) {
                return 'cougar';
            }
        }));

        static::assertFalse(which_of(5, [2, 7, 13], static function ($subject, $option) {
            if ($option % $subject === 0) {
                return 'cougar';
            }
        }));
    }
}
