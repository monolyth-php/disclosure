<?php

namespace Demo;

use Disclosure\Injector;

class Multiple
{
    use Injector;

    public function __construct()
    {
        $this->inject(function($foo) {});
        $this->inject(function($baz, $bar) {});
    }
}

Multiple::register(function (&$foo, &$bar, &$baz) {
    $foo = new BasicInjection;
    $bar = new BasicInjectionInherited;
    $baz = new ChildInheritance;
});

