<?php

use Disclosure\Injector;
use Disclosure\UnregisteredException;

/**
 * Test classes
 * {{{
 */
class Basic
{
    public $bar = null;

    use Injector;

    public function __construct()
    {
        $this->inject(function (BasicInjection $bar) {});
    }
}

class BasicInheritance
{
    public $bar = null;

    use Injector;

    public function __construct()
    {
        $this->inject(function (BasicInjection $bar) {});
    }
}

class BasicInjection
{
}

class BasicInjectionInherited extends BasicInjection
{
}

/*
class Baz extends Foo
{
    use Injector;
}
*/

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testBasicHasBasicInjection()
    {
        Basic::inject(function (&$bar) {
            $bar = new BasicInjection;
        });
        $foo = new Basic;
        $this->assertInstanceOf('BasicInjection', $foo->bar);
    }

    public function testBasicInherited()
    {
        BasicInheritance::inject(function (&$bar) {
            $bar = new BasicInjectionInherited;
        });
        $foo = new BasicInheritance;
        $this->assertInstanceOf('BasicInjection', $foo->bar);
        $this->assertInstanceOf('BasicInjectionInherited', $foo->bar);
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

