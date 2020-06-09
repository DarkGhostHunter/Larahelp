<?php

namespace Tests;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class HelpersTest extends TestCase
{
    public function test_collect_lazy()
    {
        $lazy = collect_lazy(function () {
            $array = ['foo', 'bar', 'quz', 'qux'];

            reset ($array);

            while (current($array) !== false) {
                yield current($array);
                next($array);
            }
        });

        $this->assertInstanceOf(LazyCollection::class, $lazy);

        $lazy->each(function ($value, $key) {
            switch ($key) {
                case 0:
                    return $this->assertSame('foo', $value);
                case 1:
                    return $this->assertSame('bar', $value);
                case 2:
                    return $this->assertSame('quz', $value);
                case 3:
                    return $this->assertSame('qux', $value);
            }
        });
        $this->assertCount(4, $lazy);

        $lazy = collect_lazy(10, function ($iteration) {
            return 'foo.' . $iteration;
        });

        $this->assertInstanceOf(LazyCollection::class, $lazy);
        $this->assertCount(10, $lazy);

        $lazy->each(function ($iteration, $key) {
            $this->assertSame('foo.' . ($key + 1), $iteration);
            $this->assertGreaterThanOrEqual(0, $key);
            $this->assertLessThan(10, $key);
        });

        $lazy = collect_lazy(1, 10);

        $this->assertInstanceOf(LazyCollection::class, $lazy);
        $this->assertCount(10, $lazy);

        $lazy->each(function ($iteration, $key) {
            $this->assertSame(($key + 1), $iteration);
            $this->assertGreaterThanOrEqual(0, $key);
            $this->assertLessThan(10, $key);
        });
    }

    public function test_collect_times()
    {
        $collection = collect_times(10);
        $this->assertCount(10, $collection);
        $collection->each(function ($iteration, $key) {
            $this->assertSame($key + 1, $iteration);
        });

        $random = collect();

        $collection = collect_times(10, function () use ($random) {
            $random->push($rand = Str::random(5));
            return $rand;
        });

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(10, $collection);
        $this->assertCount(10, $random);

        foreach ($collection as $key => $string) {
            $this->assertSame($random[$key], $string);
        }
    }

    public function test_data_transform()
    {
        $array = [
            'foo' => [
                'bar', 'quz' => [
                    'qux' => 'quuz'
                ]
            ]
        ];

        data_transform($array, 'foo.quz.qux', function ($value) {
            return $value . '.quux';
        });

        $this->assertSame($array['foo']['quz']['qux'], 'quuz.quux');

        data_transform($array, 'foo.quz.qux', function ($value) {
            $value . '.quux';
        });

        $this->assertNull($array['foo']['quz']['qux']);
    }

    public function test_enclose()
    {
        $enclose = enclose('foo');

        $this->assertInstanceOf(Closure::class, $enclose);

        $this->assertSame('foo', $enclose());
        $this->assertSame('foo.bar', $enclose(function ($value) {
            return "$value.bar";
        }));
    }

    public function test_fluent()
    {
        $fluent = fluent(['foo' => 'bar']);

        $this->assertInstanceOf(Fluent::class, $fluent);
        $this->assertSame('bar', $fluent->foo);
    }

    public function test_pipeline()
    {
        $barToQuz = new class
        {
            public function handle($bar, $next)
            {
                $bar = $bar === 'bar' ? 'quz' : $bar;
                return $next($bar);
            }
        };

        $pipe = pipeline('foo', [
            function ($foo, $next) {
                $foo = $foo === 'foo' ? 'bar' : $foo;
                return $next($foo);
            },
            $barToQuz
        ]);

        $this->assertSame('quz', $pipe);

        $pipe = pipeline('foo', [
            function ($foo, $next) {
                $foo = $foo === 'foo' ? 'bar' : $foo;
                return $next($foo);
            },
            $barToQuz
        ], function ($result) {
            return $result === 'quz' ? 'qux' : $result;
        });

        $this->assertSame('qux', $pipe);
    }

    public function test_throttle()
    {
        $randomFunction = function () {
            return Str::random();
        };

        $first = throttle('foo', $randomFunction);

        $this->assertTrue($first);
        $this->assertTrue(throttle('foo', $randomFunction));
        $this->assertTrue(throttle('bar', $randomFunction));

        $second = throttle('quz', $randomFunction, 1, 60);

        $this->assertTrue($second);
        $this->assertFalse(throttle('quz', $randomFunction, 1, 60));
        $this->assertTrue(throttle('qux', $randomFunction, 1, 60));
    }

    public function test_unless()
    {
        $result = unless(false, 'foo');
        $this->assertSame('foo', $result);
        $result = unless(true, 'foo');
        $this->assertNull($result);

        $result = unless(function () {
            return false;
        }, 'bar');
        $this->assertSame('bar', $result);

        $result = unless(function () {
            return true;
        }, 'bar');
        $this->assertNull($result);

        $result = unless(function () {
            return false;
        }, function () {
            return 'quz';
        });
        $this->assertSame('quz', $result);

        $result = unless(function () {
            return true;
        }, function () {
            return 'quz';
        });
        $this->assertNull($result);
    }

    public function test_when()
    {
        $result = when(true, 'foo');
        $this->assertSame('foo', $result);
        $result = when(false, 'foo');
        $this->assertNull($result);

        $result = when(function () {
            return true;
        }, 'bar');
        $this->assertSame('bar', $result);

        $result = when(function () {
            return false;
        }, 'bar');
        $this->assertNull($result);

        $result = when(function () {
            return true;
        }, function () {
            return 'quz';
        });
        $this->assertSame('quz', $result);

        $result = when(function () {
            return false;
        }, function () {
            return 'quz';
        });
        $this->assertNull($result);
    }

    public function test_none_of()
    {
        $this->assertTrue(none_of('foo', ['bar', 'quz', 'qux']));
        $this->assertFalse(none_of('foo', ['bar', 'quz', 'qux', 'foo']));

        $this->assertTrue(none_of(5, [2, 7, 13], function ($subject, $option) {
            return $option % $subject === 0;
        }));
        $this->assertFalse(none_of(5, [2, 7, 15], function ($subject, $option) {
            return $option % $subject === 0;
        }));
    }

    public function test_random_bool()
    {
        $this->assertFalse(random_bool(0));

        $results = collect_times(10, function () {
            return random_bool(1);
        });

        $results->each(function ($item) {
            $this->assertIsBool($item);
        });

        $this->assertLessThan(10, $results->filter()->count());
        $this->assertGreaterThan(0, $results->reject()->count());
        $this->assertNotSame($results->filter()->count(), $results->reject()->count());

        $results = collect_times(10, function () {
            return random_bool(-1);
        });

        $results->each(function ($item) {
            $this->assertIsBool($item);
        });

        $this->assertLessThan(10, $results->filter()->count());
        $this->assertGreaterThan(0, $results->reject()->count());
        $this->assertNotSame($results->filter()->count(), $results->reject()->count());
    }

    public function test_random_unique()
    {
        $random_unique = random_unique(10, function () {
            return random_int(1, 2);
        });

        $this->assertLessThan(10, count($random_unique));

        $random_unique = random_unique(10, function () {
            return random_int(1, 10);
        }, true);

        $this->assertCount(10, $random_unique);
    }

    public function test_swap_vars()
    {
        $foo = 'foo';
        $bar = 'bar';

        $this->assertSame('bar', swap_vars($foo, $bar));
        $this->assertSame('bar', $foo);
        $this->assertSame('foo', $bar);
    }

    public function test_which_of()
    {
        $this->assertSame('foo', which_of('foo', ['bar', 'quz', 'qux', 'foo']));
        $this->assertFalse(which_of('foo', ['bar', 'quz', 'qux']));

        $this->assertSame(15, which_of(5, [2, 7, 15], function ($subject, $option) {
            return $option % $subject === 0;
        }));

        $this->assertFalse(which_of(5, [2, 7, 13], function ($subject, $option) {
            return $option % $subject === 0;
        }));
    }

    public function test_while_sleep()
    {
        $start = time();
        $collection = while_sleep(4, 1000, function () {
            return microtime(false);
        });
        $end = time();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(4, $collection);
        $this->assertSame(3, $end - $start);
    }
}