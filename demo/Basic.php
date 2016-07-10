<?php

namespace Monolyth\Disclosure\Demo;

use Monolyth\Disclosure\Injector;

class Basic
{
    public $foo = null;

    use Injector;

    public function __construct()
    {
        $this->inject(function ($foo, $bar) {}, 'baz');
    }
}

