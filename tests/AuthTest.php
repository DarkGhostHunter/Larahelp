<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class AuthTest extends TestCase
{
    public function test_user(): void
    {
        static::assertNull(user());

        $this->actingAs($user = new User());

        static::assertSame($user, user());

        config()->set('auth.guards', [
            'api' => [
                'driver' => 'token',
                'provider' => 'users',
                'hash' => false,
            ],
        ]);

        $this->actingAs($user, 'api');

        static::assertSame($user, user('api'));
    }

    public function test_logged_in(): void
    {
        $executed = false;

        $user = new User();

        $call = logged_in($user, static function ($authenticatable) use (&$executed, $user): mixed {
            static::assertSame($user, $authenticatable);
            static::assertSame($user, auth()->guard()->user());
            static::assertFalse(auth()->viaRemember());
            $executed = true;
            return 'foo';
        });

        static::assertSame('foo', $call);
        static::assertTrue($executed);
        static::assertNull(user());

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->rememberToken();
        });

        $call = logged_in($user, static function ($authenticatable) use (&$executed, $user): void {
            static::assertSame($user, $authenticatable);
            static::assertNull(auth()->guard('api')->user());
            static::assertSame($user, auth()->guard('web')->user());
            $executed = true;
        }, 'web');

        static::assertNull($call);
        static::assertTrue($executed);
        static::assertNull(user());
    }

    public function test_exception_logged_in(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('A user [0] was already authenticated');

        $this->actingAs($user = new User());
        $user->id = 'foo';

        logged_in($user, static function (): void {
            //
        });
    }
}
