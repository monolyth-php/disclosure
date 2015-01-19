<?php

namespace Disclosure;
use ReflectionFunction;

function _($class, array $injects)
{
    static $map = [];

    $resolve = function ($for, $class) use ($map) {
    };

    $tree = [];
    do {
        $tree[] = $class;
    } while ($class = get_parent_class($class));
    $class = $tree[0];

    if (!isset($map[$class])) {
        $map[$class] = [];
    }
    var_dump($class, $map[$class]);
    foreach ($injects as $name => &$cname) {
        if (!is_scalar($cname)) {
            $map[$class][$name] = $cname;
            continue;
        }
        foreach ($map[$class] as $key => $resolved) {
            if ($resolved instanceof $cname) {
                $cname = $resolved;
                continue 2;
            }
        }
        if (!is_bool($cname)) {
            if (!isset($map[$class][$name])) {
                $map[$class][$name] = $cname;
            } elseif (!is_string($map[$class][$name])
                && get_class($map[$class][$name]) == $cname
            ) {
                $cname = $map[$class][$name];
            }
        }
        /*
        foreach ($tree as $parent) {
            if (isset($map[$parent])) {
                foreach ($map[$parent] as $previous) {
                    if (!is_string($previous)
                        && !is_object($cname)
                        && get_class($previous) == $cname
                    ) {
                        $cname = $previous;
                        break 2;
                    }
                }
            }
        }
        */
        if (is_string($cname) && class_exists($cname)) {
            $cname = new $cname;
            $map[$class][$name] = $cname;
        }
    }
    var_dump($class, $map[$class]);
    return $injects;
}

trait Injector
{
    public function inject(callable $inject)
    {
        $static = !isset($this) || __CLASS__ != get_class($this);
        $class = $static ? __CLASS__ : get_class($this);
        $reflection = new ReflectionFunction($inject);
        $parameters = $reflection->getParameters();
        $classes = [];
        foreach ($parameters as $idx => $parameter) {
            if ($requestedclass = $parameter->getClass()) {
                $classes[$parameter->name] = $requestedclass->name;
            } else {
                $classes[$parameter->name] = true;
            }
        }
        $injections = _($class, $classes);
        $previous = $injections;
        var_dump(1, $injections);
        $reflection->invokeArgs($injections);
        var_dump(2, $injections);
        if ($previous != $injections) {
            _($class, $injections);
        }
        var_dump(3, $injections);
        $missing = false;
        if (!$static) {
            foreach ($injections as $requested => $value) {
                if (is_string($value)) {
                    $missing = true;
                    continue;
                }
                $this->$requested = $value;
            }
        }
        if ($missing) {
            throw new UninjectableException;
        }
    }
}

