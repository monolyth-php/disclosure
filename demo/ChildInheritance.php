<?php

namespace Demo;

use Disclosure\Injector;

class ChildInheritance extends ParentInheritance
{
    public function __construct()
    {
        $this->inject(function ($bar) {});
    }
}

