<?php

namespace Monolyth\Disclosure;

use ReflectionClass;

abstract class Factory
{
    public static function build(string $object, ...$arguments) : object
    {
        $reflection = new ReflectionClass($object);
        $arguments = self::getArgumentsForClassConstructor($reflection, $arguments);
        $object = $reflection->newInstance(...$arguments);
        if (in_array(Injector::class, $reflection->getTraitNames())) {
            $object->inject();
        }
        return $object;
    }

    public static function getArgumentsForClassConstructor(ReflectionClass $reflection, array $arguments)
    {
        static $container = new Container;
        $constructor = $reflection->getMethod('__construct');
        $parameters = $constructor->getParameters();
        $args = [];
        foreach ($parameters as $parameter) {
            if ($parameter->isPromoted()) {
                $attributes = $parameter->getAttributes(Depends::class);
                if ($attributes) {
                    $args[] = $container->get($parameter->getName());
                    continue;
                }
            }
            $args[] = array_shift($arguments);
        }
        return $args;
    }
}

