<?php

namespace Demo;

use Monolyth\Disclosure\{ Depends, Injector };

class WithAttributes1
{
    use Injector;

    #[Depends]
    public BasicInjection1 $foo;
}

