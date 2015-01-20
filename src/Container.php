<?php

namespace Disclosure;
use ReflectionClass;
use ReflectionFunction;

class Container
{
    private static $map = [];

    public static function inject($class, callable $inject)
    {
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
        $injections = self::resolve($class, $classes);
        foreach ($injections as $key => &$value) {
            if (isset($classes[$key])) {
                $classes[$key] = $value;
            }
        }
        $reinject = $reflection->invokeArgs($classes);
        if ($reinject) {
            foreach ($classes as &$value) {
                $reflected = new ReflectionClass($value);
                $constructor = $reflected->getConstructor();
                if ($constructor) {
                    $args = $constructor->getParameters();
                    $makeNew = true;
                    foreach ($args as $parameter) {
                        if (!$parameter->isOptional()) {
                            $makeNew = false;
                            break;
                        }
                    }
                } else {
                    $makeNew = false;
                }
                if (!$makeNew) {
                    $value = clone $value;
                } else {
                    $value = $reflected->newInstance();
                }
            }
        }
        self::resolve($class, $classes);
        return $classes;
    }
    
    private static function resolve($class, array $injects)
    {
        $tree = ['*' => '*'];
        if ($class != '*') {
            $tree += [$class => $class] + class_parents($class);
        }
        if (!isset(self::$map[$class])) {
            self::$map[$class] = [];
        }
        foreach ($injects as $name => &$cname) {
            if (!is_scalar($cname)) {
                self::$map[$class][$name] = $cname;
                continue;
            }
            if (is_bool($cname) && self::resolveByName($name, $cname, $tree)) {
                continue;
            }
            foreach (self::$map[$class] as $key => $resolved) {
                if (!class_exists($cname) || $resolved instanceof $cname) {
                    $cname = $resolved;
                    continue 2;
                }
            }
            if (!is_bool($cname)) {
                if (!isset(self::$map[$class][$name])) {
                    self::$map[$class][$name] = $cname;
                } elseif (!is_string(self::$map[$class][$name])
                    && get_class(self::$map[$class][$name]) == $cname
                ) {
                    $cname = self::$map[$class][$name];
                }
            }
            if (self::resolveByParents($cname, $tree)) {
                continue;
            }
            if ($class != '*') {
                if (self::resolveByParents($cname, class_implements($class))) {
                    continue;
                }
                if (self::resolveByParents($cname, class_uses($class))) {
                    continue;
                }
            }
            if (is_string($cname) && class_exists($cname)) {
                $cname = new $cname;
                self::$map[$class][$name] = $cname;
            } elseif (!is_scalar($cname)) {
                self::$map[$class][$name] = $cname;
            }
        }
        return $injects;
    }

    private static function resolveByParents(&$class, array $parents)
    {
        foreach ($parents as $parent) {
            if (isset(self::$map[$parent])) {
                foreach (self::$map[$parent] as $previous) {
                    if (!is_string($previous)
                        && !is_object($class)
                        && is_object($previous)
                        && get_class($previous) == $class
                    ) {
                        $class = $previous;
                        return true;
                    }
                }
            }
            if ($parent != '*'
                && self::resolveByParents($class, class_implements($parent))
            ) {
                return true;
            }
        }
        return false;
    }

    private static function resolveByName($name, &$class, array $classes)
    {
        foreach ($classes as $parent) {
            if (isset(self::$map[$parent][$name])) {
                $class = self::$map[$parent][$name];
                return true;
            }
        }
        return false;
    }
}

