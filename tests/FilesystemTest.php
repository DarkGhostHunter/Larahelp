<?php

namespace Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

use const DIRECTORY_SEPARATOR as DS;

class FilesystemTest extends TestCase
{
    protected function setUp() : void
    {
        $this->afterApplicationCreated(static function () {
            $class = app_path('TestClass.php');
            if (! File::exists($class)) {
                File::replace($class, <<<EOT
<?php

namespace App;

class TestClass
{
    public function something()
    {
        // ...
    }

    protected function privateFunction(int \$argument, User \$user, string \$optional = null)
    {

    }
}
EOT
                );
            }

            require_once $class;
        });

        parent::setUp();
    }

    public function test_dot_path(): void
    {
        static::assertSame('foo.Bar.quz', dot_path('\foo\Bar/quz'));
        static::assertSame('foo.Bar.quz', dot_path('foo\Bar/quz'));
    }

    public function test_files(): void
    {
        static::assertInstanceOf(Filesystem::class, files());

        $mock = $this->mock('files');

        $mock->shouldReceive('files')
            ->once()
            ->with(base_path('foo/bar'))
            ->andReturn(['foo', 'bar']);

        $mock->shouldReceive('allFiles')
            ->once()
            ->with(base_path('baz/qux'))
            ->andReturn(['baz', 'qux', 'quuz']);

        $files = files('foo/bar');

        static::assertInstanceOf(Collection::class, $files);
        static::assertCount(2, $files);

        $files = files('baz/qux', true);

        static::assertInstanceOf(Collection::class, $files);
        static::assertCount(3, $files);
    }

    public function test_undot_path(): void
    {
        static::assertSame('foo' . DS . 'Bar' . DS . 'quz', undot_path('foo.Bar.quz'));
        static::assertSame('foo' . DS . 'Bar' . DS . 'quz', undot_path('foo.Bar.quz'));
    }
}
