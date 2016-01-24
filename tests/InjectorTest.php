<?php

namespace Disclosure\Test;

use Disclosure\Injector;
use Disclosure\UnregisteredException;
use Demo;
use Gentry\Property;
use Gentry\Group;

/**
 * @Feature Injector should inject requested classes
 */
class InjectorTest
{
    /**
     * @Scenario {0}::$bar is a BasicInjection
     */
    public function bar(Demo\Basic $foo)
    {
        return function ($result) {
            return $result instanceof Demo\BasicInjection;
        };
    }

    /**
     * @Scenario {0}::$bar gets injected through parent inheritance
     */
    public function parentInheritance(Demo\ChildInheritance $foo)
    {
        return new Demo\BasicInjection;
    }

    public function multiple(Demo\Multiple $test)
    {
        return new Group($this, $test, [
            /**
             * @Scenario {0}::$foo is an instance of BasicInjection
             */
            function () { return new Demo\BasicInjection; },
            /**
             * @Scenario {0}::$bar is an instance of BasicInjectionInherited
             */
            function () { return new Demo\BasicInjectionInherited; },
            /**
             * @Scenario {0}::$baz is an instance of ChildInheritance
             */
            function () { return new Demo\ChildInheritance; },
        ]);
    }
}

