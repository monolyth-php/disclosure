<?php

namespace Monolyth\Disclosure;

use ReflectionObject;
use ReflectionFunction;
use ReflectionClass;
use ReflectionProperty;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Injector trait classes with dependencies can "use".
 */
trait Injector
{
    /**
     * Inject the specified dependencies into this object. Each argument can be
     * either a string containing the name of the dependency, or a callable
     * where each argument defines that name.
     *
     * @param string|callable ...$injects What to inject.
     * @return void
     * @throws Psr\Container\NotFoundExceptionInterface if _any_ of the
     *  requested dependencies could not be resolved.
     * @see Monolyth\Disclosure\Container::get
     */
    public function inject(...$injects) : void
    {
        $container = new Container;
        $requested = [];
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(~ReflectionProperty::IS_STATIC);
        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Depends::class);
            if ($attributes) {
                $requested[] = $property->getName();
            }
        }
        foreach ($injects as $inject) {
            if (is_string($inject)) {
                $requested[] = $inject;
            } elseif (is_callable($inject)) {
                $reflection = new ReflectionFunction($inject);
                foreach ($reflection->getParameters() as $param) {
                    $requested[] = $param->name;
                }
            }
        }
        foreach (array_unique($requested) as $dependency) {
            if (!isset($this->$dependency)) {
                $this->$dependency = $container->get($dependency);
            }
        }
    }
}

