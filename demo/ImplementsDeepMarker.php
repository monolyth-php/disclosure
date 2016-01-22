<?php

namespace Demo;

use Disclosure\Injector;

class ImplementsDeepMarker implements DeepMarker
{
    use Injector;

    public function __construct()
    {
        $this->inject(function (BasicInjection $bar) {});
    }
}

