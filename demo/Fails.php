<?php

namespace Monolyth\Disclosure\Demo;

use Monolyth\Disclosure\Injector;

class Fails
{
    public function __construct()
    {
        $this->inject('baz');
    }
}

