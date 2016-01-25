<?php

namespace Demo;

use Disclosure\Injector;

class Basic
{
    public $foo = null;

    use Injector;

    public function __construct()
    {
        $this->inject(function ($foo, $bar) {}, 'baz');
    }
}

