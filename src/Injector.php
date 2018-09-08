<?php

namespace Monolyth\Disclosure;

use ReflectionFunction;
use ReflectionClass;
use Psr\Container\Exception\NotFoundExceptionInterface;

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
     * @throws Psr\Container\Exception\NotFoundExceptionInterface if _any_ of
     *  the requested dependencies could not be resolved.
     */
    public function inject(...$injects) : void
    {
        $container = new Container;
        $requested = [];
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
        foreach ($requested as $dependency) {
            $this->$dependency = $container->get($dependency);
        }
    }

    /**
     * For classes that expect dependencies in their constructor, you can use
     * this method instead of "new" with lots of Container::get calls.
     *
     * Type-hinted arguments _must_ match a dependency. If no dependency exists,
     * a new instance will be passed.
     *
     * @return object An object of the same class as called on, with dependencies
     *  injected through its constructor.
     * @throws Disclosure\TypeMismatchException if the retrieved dependency does
     *  not satisfy the argument's type hint.
     * @throws Psr\Container\Exception\NotFoundExceptionInterface if _any_ of
     *  the requested dependencies could not be resolved.
     */
    public static function resolve() : object
    {
        $reflection = new ReflectionClass(__CLASS__);
        $constructor = $reflection->getConstructor();
        $args = [];
        $container = new Container;
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->name;
            $e = $inject = $class = null;
            try {
                $inject = $container->get($name);
            } catch (NotFoundExceptionInterface $e) {
            }
            if ($class = $parameter->getClass()) {
                $instance = $class->getName();
                if (isset($inject)) {
                    if ($inject instanceof $instance) {
                        $args[] = $inject;
                        continue;
                    } else {
                        throw new TypeMismatchException(get_class($inject));
                    }
                }
                if ($class->implementsInterface(
                    'Monolyth\Disclosure\Injectable'
                )) {
                    $args[] = $class::resolve();
                } else {
                    $args[] = $class->newInstance();
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } elseif (isset($e)) {
                throw $e;
            } elseif (isset($inject)) {
                $args[] = $inject;
            }
        }
        return $reflection->newInstanceArgs($args);
    }
}

