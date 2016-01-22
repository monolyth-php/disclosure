<?php

namespace Demo;

use Disclosure\Injector;

class ParentInheritance
{
    use Injector;
}

ParentInheritance::inject(function (&$bar) {
    $bar = new BasicInjection;
});

