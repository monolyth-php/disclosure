<?php

namespace Disclosure;
use ReflectionFunction;

trait Injector
{
    public function inject(callable $inject)
    {
        $static = !isset($this) || __CLASS__ != get_class($this);
        $class = $static ? __CLASS__ : get_class($this);
        $reflection = new ReflectionFunction($inject);
        $parameters = $reflection->getParameters();
//        $injectable = $inject();
        // For single arguments, allow shorthand of single value return.
        /*
        if (!is_array($injectable)) {
            $injectable = [$injectable];
        }
        if ($static) {
            $inject = [];
            foreach ($parameters as $idx => $value) {
                var_dump($value);
                $inject[$value->name] = $injectable[$idx];
            }
            var_dump(get_parent_class($class));
            Container::register($class, $inject);
        }
        var_dump($parameters);
        if ($static) {
            Container::
        var_dump($static, $class);
        /*
        $args = func_get_args();
        if (is_callable(end($args))) {
            $callback = array_pop($args);
        }
        $deps = [];
        $missing = false;
        $container = Container::instance();
        foreach ($args as $arg) {
            try {
                $deps[$arg] = call_user_func(
                    $container->get($arg)
                );
            } catch (UnregisteredException $e) {
                $missing = true;
                $deps[$arg] = null;
            }
        }
        if (isset($callback)) {
            $deps = call_user_func_array($callback, $deps);
        }
        foreach ($deps as $name => $value) {
            if (isset($value)) {
                $this->$name = $value;
            }
        }
        if ($missing) {
            throw new UnregisteredException;
        }
        */
    }
}

