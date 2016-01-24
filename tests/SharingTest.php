<?php

namespace Disclosure\Test;

use Disclosure\Container;
use Demo;

/**
 * @Feature Classes should share injections
 */
class SharingTest
{
    /**
     * @Scenario {0}::$bar is the same class but a different instance than $foo2->bar
     */
    public function testEquality(Demo\Basic $foo, Demo\Basic2 $foo2)
    {
        return function ($result) use ($foo2) {
            return $result == $foo2->bar && $result !== $foo2->bar;
        };
    }
}

