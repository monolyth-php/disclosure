<?php

use Disclosure\Injector;
use Disclosure\UnregisteredException;

class Foo
{
    public $bar = null;

    use Injector;
}

class Bar
{
}

class Baz extends Foo
{
    use Injector;
}

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testFooHasBar()
    {
        Foo::inject(function ($bar) {
            return new Bar;
        });
        Baz::inject(function ($bar) {
            return new Bar;
        });
        $foo = new Foo;
        $this->assertInstanceOf('Bar', $foo->bar);
    }

    /*
    public function testFooWithAlias()
    {
        $foo = new Foo;
        $this->assertInstanceOf('Baz', $foo->Alias);
    }

    public function testService()
    {
        $foo = new Foo;
        $this->assertSame($foo->Service, $foo->Service2);
        $this->assertNotSame($foo->Service, $foo->Service3);
    }

    public function testUnregistered()
    {
        $e = null;
        try {
            $foo = new FooUnregistered;
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('Disclosure\UnregisteredException', $e);
    }
    */
}

