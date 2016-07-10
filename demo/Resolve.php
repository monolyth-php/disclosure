<?php

namespace Monolyth\Disclosure\Demo;

use Monolyth\Disclosure\Injector;
use Monolyth\Disclosure\Injectable;

class Resolve implements Injectable
{
    use Injector;

    public function __construct(BasicInjection1 $foo, BasicInjection2 $fuzz, $fizz)
    {
        $this->foo = $foo;
        $this->fuzz = $fuzz;
        $this->fizz = $fizz;
    }
}

