<?php

namespace Disclosure;
use BadMethodCallException;

class Container
{
    private $registered = [];

    public function register($arg1, $arg2 = null)
    {
        if (isset($arg2)) {
            $name = $arg1;
            $class = $arg2;
        } else {
            $class = $arg1;
            // Guesstimate dependency name:
            if (is_string($arg1)) {
                $parts = explode('\\', $arg1);
                $name = array_pop($parts);
            } elseif (is_array($arg1) && is_callable($arg1)) {
                $parts = explode('\\', $arg1[0]);
                $name = array_pop($parts);
            } elseif (is_object($arg1)) {
                if (is_callable($arg1)) {
                    $name = count($this->registered) + 1;
                } else {
                    $cname = get_class($arg1);
                    $parts = explode('\\', $cname);
                    $name = array_pop($parts);
                }
            } else {
                throw new BadMethodCallException;
            }
        }
        $this->registered[$name] = $class;
        return $name;
    }

    public function unregister($name)
    {
        unset($this->registered[$name]);
    }

    public function get($name)
    {
        return function() use($name) {
            if (!isset($this->registered[$name])) {
                throw new UnregisteredException;
            }
            $found = $this->registered[$name];
            if (is_callable($found)) {
                return $found();
            } elseif (is_object($found)) {
                return $found;
            } elseif (class_exists($found)) {
                return new $found;
            }
        };
    }
}
