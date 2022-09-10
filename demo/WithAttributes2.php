<?php

namespace Demo;

use Monolyth\Disclosure\Depends;

class WithAttributes2
{
    public function __construct(
        #[Depends]
        public BasicInjection1 $foo
    ) {
    }
}

