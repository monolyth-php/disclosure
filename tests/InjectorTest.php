<?php

namespace Disclosure\Test;

use Disclosure\Injector;
use Disclosure\UnregisteredException;
use Demo;
use Gentry\Property;
use Gentry\Group;

/**
 * @Description Injector should inject requested classes
 */
class InjectorTest
{
    /**
     * @Description {0}::$bar is a BasicInjection
     */
    public function bar(Demo\Basic $foo)
    {
        return function ($result) {
            return $result instanceof Demo\BasicInjection;
        };
    }

    /*
    public function testMultipleInjections()
    {
        Basic::inject(function (&$bar, &$baz) {
            $bar = new BasicInjection;
            $baz = new BasicInjectionInherited;
        });
        $foo = new Basic;
        $foo->inject(function (BasicInjectionInherited $baz) {});
        $this->assertInstanceOf('BasicInjection', $foo->bar);
        $this->assertInstanceOf('BasicInjectionInherited', $foo->baz);
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
    */

    /**
     * @Description {0}::$bar gets injected through parent inheritance
     */
    public function parentInheritance(Demo\ChildInheritance $foo)
    {
        return new Demo\BasicInjection;
    }

    public function multiple(Demo\Multiple $test)
    {
        return new Group($this, $test, [
            /**
             * @Description {0}::$foo is an instance of BasicInjection
             */
            function () { return new Demo\BasicInjection; },
            /**
             * @Description {0}::$bar is an instance of BasicInjectionInherited
             */
            function () { return new Demo\BasicInjectionInherited; },
            /**
             * @Description {0}::$baz is an instance of ChildInheritance
             */
            function () { return new Demo\ChildInheritance; },
        ]);
    }

    /*
    public function testMultiple()
    {
        Multiple::inject(function (&$foo, &$bar, &$baz) {
            $foo = new BasicInjection;
            $bar = new BasicInjectionInherited;
            $baz = new ChildInheritance;
        });
        $test = new Multiple;
        $this->assertTrue(get_class($test->foo) == 'BasicInjection');
        $this->assertTrue(get_class($test->bar) == 'BasicInjectionInherited');
        $this->assertTrue(get_class($test->baz) == 'ChildInheritance');
    }
    */
}

