<?php

namespace Monolyth\Disclosure;

use ReflectionClass;

trait Mother
{
    public function callParentConstructor(mixed ...$arguments) : void
    {
        $arguments = Factory::getArgumentsForClassConstructor(new ReflectionClass(get_parent_class($this)), $arguments);
        parent::__construct(...$arguments);
    }
}

