<?php

namespace Demo;

use Disclosure\Injector;

class UsesTrait
{
    use TraitDependency;
    use Injector;

    public function __construct()
    {
        $this->inject(function(BasicInjection $bar) {});
    }
}

