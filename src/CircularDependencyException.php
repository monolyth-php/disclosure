<?php

namespace Monolyth\Disclosure;

use LogicException;

class CircularDependencyException extends LogicException
{
    public function __construct(array $stack)
    {
        parent::__construct("Stack trace: ".implode(' -> ', $stack));
    }
}

