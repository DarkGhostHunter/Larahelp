<?php

namespace Tests;

use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Hashing\Hasher;

class ServicesTest extends TestCase
{
    public function test_artisan()
    {
        $this->assertInstanceOf(Kernel::class, artisan());

        Artisan::command('foo', function () {
            return 'bar';
        });

        $this->assertSame(0, artisan('foo'));
    }

    public function test_hasher()
    {
        $this->assertInstanceOf(Hasher::class, hasher());

        config(['app.key' => '4MMWpU0EeF2v0GXS464EMSDK2161XXEA']);

        $hashed = hasher('foo');

        $this->assertTrue(hasher()->check('foo', $hashed));
    }

    public function test_user()
    {
        $this->assertNull(user());

        $this->actingAs($user = new User);

        $this->assertEquals($user, user());
    }
}