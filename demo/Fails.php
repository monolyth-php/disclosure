<?php

namespace Demo;

use Disclosure\Injector;

class Fails
{
    public function __construct()
    {
        $this->inject('baz');
    }
}

