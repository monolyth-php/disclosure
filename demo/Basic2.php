<?php

namespace Demo;

use Disclosure\Injector;

class Basic2
{
    public $bar = null;

    use Injector;

    public function __construct()
    {
        $this->inject(function (BasicInjection $bar) {});
    }
}

