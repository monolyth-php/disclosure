<?php

namespace Demo;

use Disclosure\Injector;

class ParentInheritance
{
    use Injector;
}

ParentInheritance::register(function (&$bar) {
    $bar = new BasicInjection;
});

