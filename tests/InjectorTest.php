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
 * @Feature Injector should inject requested classes
 */
class InjectorTest
{
    /**
     * @Scenario {0}::$foo is a BasicInjection1
     */
    public function fooIsSet(Demo\Basic $foo)
    {
        return new Demo\BasicInjection1;
    }

    /**
     * @Scenario {0}::$bar is a BasicInjection2
     */
    public function barIsSet(Demo\Basic $foo)
    {
        return new Demo\BasicInjection2;
    }

    /**
     * @Scenario {0}::$baz is a BasicInjection3 injected via simple string
     */
    public function bazIsSet(Demo\Basic $foo)
    {
        return new Demo\BasicInjection3;
    }

    /**
     * @Scenario {0}::inject should throw an exception if the dependency is unknown
     */
    public function unknown(Demo\Basic $foo, $baz = 'whatever')
    {
        throw new NotFoundException('baz');
    }

    /**
     * @Scenario {0}::$bar is the same class and instance as $foo2->bar
     *
     */
    public function testEquality(Demo\Basic $foo, Demo\Basic $foo2)
    {
        return function ($result) use ($foo2) {
            return $result === $foo2->bar;
        };
    }

    /**
     * @Scenario {0}::resolve should instantiate a constructor-injected class
     */
    public function resolving(Demo\Resolve $foo = null)
    {
        return function ($result) {
            return $result->foo instanceof Demo\BasicInjection1
                && $result->fuzz instanceof Demo\BasicInjection2
                && $result->fizz instanceof Demo\DeepInjection
                && $result->fizz->bar instanceof Demo\BasicInjection2;
        };
    }
}

