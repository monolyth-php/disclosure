<?php

namespace Disclosure;
use ReflectionClass;

trait Injector
{
    public function inject(callable $inject, $reinject = false)
    {
        $cname = __CLASS__;
        $static = !isset($this) || !($this instanceof $cname);
        $class = $static ? __CLASS__ : get_class($this);
        $injections = Container::inject($class, $inject);
        $missing = false;
        foreach ($injections as $requested => $value) {
            if (is_string($value)) {
                $missing = true;
                continue;
            }
            if (!$static) {
                if ($reinject) {
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
                $this->$requested = $value;
            }
        }
        if ($missing) {
            throw new UninjectableException;
        }
    }
}

