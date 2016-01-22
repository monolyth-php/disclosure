<?php

namespace Demo;

use Disclosure\Injector;

class Reinjectme
{
    use Injector;

    public function __construct()
    {
        $this->inject(function(ArgsObject $bar) { return true; });
    }
}

