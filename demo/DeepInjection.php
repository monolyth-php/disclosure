<?php

namespace Monolyth\Disclosure\Demo;

use Monolyth\Disclosure\Injector;
use Monolyth\Disclosure\Injectable;

class DeepInjection implements Injectable
{
    use Injector;

    public function __construct()
    {
        $this->inject('bar');
    }
}

