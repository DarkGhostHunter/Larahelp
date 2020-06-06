<?php

namespace Tests;

use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\Client\Response as HttpResponse;

class HttpTest extends TestCase
{
    public function test_created()
    {
        $user = (new User)->forceFill(['name' => 'foo']);
        $user->wasRecentlyCreated = true;

        $created = created($user);

        $this->assertInstanceOf(Response::class, $created);
        $this->assertSame('{"name":"foo"}', $created->getContent());
        $this->assertSame(201, $created->getStatusCode());

        $created = created(null, 200);
        $this->assertSame('', $created->getContent());
        $this->assertSame(200, $created->getStatusCode());

        $this->assertSame('bar', created(null, 200, ['foo' => 'bar'])->headers->get('foo'));
    }

    public function test_http()
    {
        $fake = Http::fake();

        $response = http('GET', 'https://example.com/test', ['foo' => 'bar']);

        $this->assertInstanceOf(HttpResponse::class, $response);

        $fake->assertSent(function (HttpRequest $request) {
            return $request->method() === 'GET'
                && $request->url() === 'https://example.com/test?foo=bar'
                && $request->body() === '';
        });

        $this->assertInstanceOf(PendingRequest::class, http());
    }

    public function test_route()
    {
        $this->assertNull(routed());

        Route::get('test', function () {
            return [routed()->getName(), get_class(routed())];
        })->name('foo');

        $this->get('test')->assertExactJson(['foo', \Illuminate\Routing\Route::class]);
    }

    public function test_route_named()
    {
        $this->assertFalse(routed_is());

        Route::get('test', function () {
            return ['foo' => routed_is('foo'), 'bar' => routed_is('bar')];
        })->name('foo');

        $this->get('test')->assertExactJson(['foo' => true, 'bar' => false]);
    }

    public function test_ok()
    {
        $this->assertInstanceOf(Response::class, $ok = ok());
        $this->assertSame(204, $ok->getStatusCode());
        $this->assertEmpty($ok->getContent());

        $this->assertSame('bar', ok(['foo' => 'bar'])->headers->get('foo'));
    }
}