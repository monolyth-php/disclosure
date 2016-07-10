<?php

namespace Monolyth\Disclosure\Test;

use Monolyth\Disclosure\Injector;
use Monolyth\Disclosure\NotFoundException;
use Monolyth\Disclosure\Demo;
use Gentry\Property;
use Gentry\Group;
use Monolyth\Disclosure\Container;

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
     * Basic::$foo is a BasicInjection1
     */
    public function fooIsSet(Demo\Basic $foo)
    {
        yield assert($foo->foo instanceof Demo\BasicInjection1);
    }

    /**
     * Basic::$bar is a BasicInjection2
     */
    public function barIsSet(Demo\Basic $foo)
    {
        yield assert($foo->bar instanceof Demo\BasicInjection2);
    }

    /**
     * Basic::$baz is a BasicInjection3 injected via simple string
     */
    public function bazIsSet(Demo\Basic $foo)
    {
        yield assert($foo->baz instanceof Demo\BasicInjection3);
    }

    /**
     * inject should throw an exception if the dependency is unknown
     */
    public function unknown(Demo\Basic $foo)
    {
        $foo->inject('whatever');
        throw new NotFoundException('baz');
    }

    /**
     * $foo->bar is the same class and instance as $foo2->bar
     *
     */
    public function testEquality(Demo\Basic $foo, Demo\Basic $foo2)
    {
        yield assert($foo->bar === $foo2->bar);
    }

    /**
     * resolve should instantiate a constructor-injected class, so that
     * $foo->foo, $foo->fuzz and $foo->fizz and $foo->fizz->bar are all of the
     * correct class.
     */
    public function resolving(Demo\Resolve &$foo = null)
    {
        yield assert($foo instanceof Demo\Resolve);
        $foo = Demo\Resolve::resolve();
        yield assert($foo->foo instanceof Demo\BasicInjection1);
        yield assert($foo->fuzz instanceof Demo\BasicInjection2);
        yield assert($foo->fizz instanceof Demo\DeepInjection);
        yield assert($foo->fizz->bar instanceof Demo\BasicInjection2);
    }
}

