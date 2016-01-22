<?php

namespace Demo;

use Disclosure\Injector;

class ImplementsMarker implements Marker
{
    use Injector;
    
    public function __construct()
    {
        $this->inject(function (BasicInjection $bar) {});
    }
}

