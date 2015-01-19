<?php

use Disclosure\Injector;
use Disclosure\UnregisteredException;

require_once __DIR__.'/classes/test.php';

class InjectorTest extends PHPUnit_Framework_TestCase
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

    public function testParentInheritance()
    {
        ParentInheritance::inject(function (&$bar) {
            $bar = new BasicInjection;
        });
        $foo = new ChildInheritance;
        $this->assertInstanceOf('BasicInjection', $foo->bar);
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

