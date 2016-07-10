<?php

namespace Monolyth\Disclosure\Demo;

use Disclosure\Injector;
use Disclosure\Injectable;

class DeepInjection implements Injectable
{
    use Injector;

    public function __construct()
    {
        $this->inject('bar');
    }
}

