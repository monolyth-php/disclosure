<?php

namespace Disclosure\Test;

use Demo;
use Disclosure\Container;
use Gentry\Group;

/**
 * @Description Classes can be reinjected
 */
class ReinjectTest
{
    public function testReinject(Demo\Reinjectme $foo)
    {
        Demo\Reinjectme::inject(function (&$bar) {
            $bar = new Demo\ArgsObject(2);
        });
        $foo2 = new Demo\Reinjectme;

        return new Group($this, $foo, [
            /**
             * @Description {0}::$bar is of the same type as $foo2->bar
             */
            function () use ($foo2) {
                return function ($result) use ($foo2) {
                    return $result == $foo2->bar;
                };
            },
            /**
             * @Description {0}::$bar is not the same instance as $foo2->bar
             */
            function () use ($foo2) {
                return function ($result) use ($foo2) {
                    return $result !== $foo2->bar;
                };
            },
        ]);
    }
}

