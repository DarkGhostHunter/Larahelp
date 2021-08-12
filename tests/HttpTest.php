<?php

namespace Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class HttpTest extends TestCase
{
    public function test_created(): void
    {
        $user = (new User)->forceFill(['name' => 'foo']);
        $user->wasRecentlyCreated = true;

        $created = created($user);

        static::assertInstanceOf(Response::class, $created);
        static::assertSame('{"name":"foo"}', $created->getContent());
        static::assertSame(201, $created->getStatusCode());

        $created = created(null, 200);
        static::assertSame('', $created->getContent());
        static::assertSame(200, $created->getStatusCode());

        static::assertSame('bar', created(null, 200, ['foo' => 'bar'])->headers->get('foo'));
    }

    public function test_ok()
    {
        static::assertInstanceOf(Response::class, $ok = ok());
        static::assertSame(204, $ok->getStatusCode());
        static::assertEmpty($ok->getContent());

        static::assertSame('bar', ok(['foo' => 'bar'])->headers->get('foo'));
    }

    public function test_route_is(): void
    {
        static::assertFalse(route_is());

        Route::get('test', function () {
            return ['foo' => route_is('foo'), 'bar' => route_is('bar')];
        })->name('foo');

        $this->get('test')->assertExactJson(['foo' => true, 'bar' => false]);
    }
}
