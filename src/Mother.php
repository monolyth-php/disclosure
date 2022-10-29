<?php

namespace Monolyth\Disclosure;

use ReflectionClass;
use ReflectionException;

trait Mother
{
    private function callParentConstructor(mixed ...$arguments) : void
    {
        $reflection = new ReflectionClass(parent::class);
        try {
            $constructor = $reflection->getMethod('__construct');
            $newArguments = Factory::getArgumentsForClassConstructor($constructor, $arguments);
            $constructor->invokeArgs($this, $newArguments);
        } catch (ReflectionException $e) {
        }
    }
}

