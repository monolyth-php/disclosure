<?php

namespace Demo;

use Monolyth\Disclosure\Depends;

class WithAttributes3
{
    public function __construct(
        #[Depends]
        public BasicInjection1 $foo,
        string $string,
        #[Depends]
        public BasicInjection2 $bar,
        int $number
    ) {
        assert($string === 'Hello world');
        assert($number === 42);
    }
}

