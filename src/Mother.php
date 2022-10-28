<?php

namespace Monolyth\Disclosure;

use ReflectionClass;
use ReflectionException;

trait Mother
{
    public function callParentConstructor(string $parentClass, mixed ...$arguments) : void
    {
        $reflection = new ReflectionClass($parentClass);
        try {
            $constructor = $reflection->getMethod('__construct');
            $newArguments = Factory::getArgumentsForClassConstructor($constructor, $arguments);
            $constructor->invokeArgs($this, $newArguments);
        } catch (ReflectionException $e) {
        }
    }
}

