<?php

namespace Disclosure\Test;

use Disclosure\Injector;
use Disclosure\NotFoundException;
use Demo;
use Gentry\Property;
use Gentry\Group;
use Disclosure\Container;

$container = new Container;
$container->register(function (&$foo, &$bar, &$baz) {
    $foo = new Demo\BasicInjection1;
    $bar = new Demo\BasicInjection2;
    $baz = new Demo\BasicInjection3;
});
$container->register(function (&$fizz) {
    $fizz = new Demo\DeepInjection;
});

/**
 * Injector should inject requested classes
 */
class InjectorTest
{
    /**
     * {0}::$foo is a BasicInjection1
     */
    public function fooIsSet(Demo\Basic $foo)
    {
        yield new Demo\BasicInjection1;
    }

    /**
     * {0}::$bar is a BasicInjection2
     */
    public function barIsSet(Demo\Basic $foo)
    {
        yield new Demo\BasicInjection2;
    }

    /**
     * {0}::$baz is a BasicInjection3 injected via simple string
     */
    public function bazIsSet(Demo\Basic $foo)
    {
        yield new Demo\BasicInjection3;
    }

    /**
     * {0}::inject should throw an exception if the dependency is unknown
     */
    public function unknown(Demo\Basic $foo, $baz = 'whatever')
    {
        yield new NotFoundException('baz');
    }

    /**
     * {0}::$bar is the same class and instance as $foo2->bar
     *
     */
    public function testEquality(Demo\Basic $foo, Demo\Basic $foo2)
    {
        yield function ($result) use ($foo2) {
            return $result === $foo2->bar;
        };
    }

    /**
     * {0}::resolve should instantiate a constructor-injected class, so that
     * {0}::$foo, {0}::$fuzz and {0}::$fizz and {0}::$fizz->bar are all of the
     * correct class.
     */
    public function resolving(Demo\Resolve &$foo = null)
    {
        yield 'is_a' => 'Demo\Resolve';
        $foo = Demo\Resolve::resolve();
        yield 'is_a' => 'Demo\BasicInjection1';
        yield 'is_a' => 'Demo\BasicInjection2';
        yield 'is_a' => 'Demo\DeepInjection';
        yield function ($result) {
            return $result->bar instanceof Demo\BasicInjection2;
        };
    }
}

