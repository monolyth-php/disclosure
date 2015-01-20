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

class Basic2
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

interface Marker
{
}

interface DeepMarker extends Marker
{
}

class ImplementsMarker implements Marker
{
    use Injector;

    public function __construct()
    {
        $this->inject(function ($bar) {});
    }
}

class ImplementsDeepMarker implements DeepMarker
{
    use Injector;

    public function __construct()
    {
        $this->inject(function ($bar) {});
    }
}

trait TraitDependency
{
}

class UsesTrait
{
    use TraitDependency;
    use Injector;

    public function __construct()
    {
        $this->inject(function($bar) {});
    }
}

class Multiple
{
    use Injector;

    public function __construct()
    {
        $this->inject(function($foo) {});
        $this->inject(function($baz, $bar) {});
    }
}

class Reinjectme
{
    use Injector;

    public function __construct()
    {
        $this->inject(function(ArgsObject $bar) {}, true);
    }
}

class ArgsObject
{
    public function __construct($param)
    {
    }
}

/** }}} */

