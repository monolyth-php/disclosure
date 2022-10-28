<?php

namespace Monolyth\Disclosure;

use ReflectionClass;
use ReflectionException;

trait Mother
{
    public function callParentConstructor(?string $parentClass = null, mixed ...$arguments) : void
    {
        if (is_null($parentClass)) {
            $parentClass = parent::class;
        }
        $reflection = new ReflectionClass($parentClass);
        try {
            $constructor = $reflection->getMethod('__construct');
            $newArguments = Factory::getArgumentsForClassConstructor($constructor, $arguments);
            $constructor->invokeArgs($this, $newArguments);
        } catch (ReflectionException $e) {
        }
    }
}

