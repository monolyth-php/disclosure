<?php

namespace Monolyth\Disclosure;

use ReflectionClass;

abstract class Factory
{
    public static function build(string $object, ...$arguments) : object
    {
        $container = new Container;
        $reflection = new ReflectionClass($object);
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
        $object = $reflection->newInstance(...$args);
        if (in_array(Injector::class, $reflection->getTraitNames())) {
            $object->inject();
        }
        return $object;
    }
}

