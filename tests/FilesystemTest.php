<?php

namespace Tests;

use App\TestClass;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\File;
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

    public function test_base_path_of()
    {
        $this->assertSame('app' . DS . 'TestClass.php', base_path_of(new TestClass()));
        $this->assertSame('app' . DS . 'TestClass.php', base_path_of(TestClass::class));
    }

    public function test_class_defined_at()
    {
        $this->assertSame(base_path('app' . DS . 'TestClass.php') . ':5', class_defined_at(TestClass::class));
        $this->assertSame(base_path('app' . DS . 'TestClass.php'), class_defined_at(TestClass::class, false));

        $this->assertSame(base_path('app' . DS . 'TestClass.php') . ':5', class_defined_at(new TestClass()));
        $this->assertSame(base_path('app' . DS . 'TestClass.php'), class_defined_at(new TestClass(), false));
    }

    public function test_dot_path()
    {
        $this->assertSame('foo.Bar.quz', dot_path('\foo\Bar/quz'));
        $this->assertSame('foo.Bar.quz', dot_path('foo\Bar/quz'));
    }

    public function test_undot_path()
    {
        $this->assertSame('foo' . DS . 'Bar' . DS . 'quz', undot_path('foo.Bar.quz'));
        $this->assertSame('foo' . DS . 'Bar' . DS . 'quz', undot_path('foo.Bar.quz'));
    }
}