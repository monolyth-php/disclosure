<?php

use Disclosure\Injector;

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

class ParentInheritance
{
    use Injector;
}

class ChildInheritance extends ParentInheritance
{
    public function __construct()
    {
        $this->inject(function ($bar) {});
    }
}

/** }}} */

