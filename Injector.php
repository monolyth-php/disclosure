<?php

namespace disclosure;

trait Injector
{
    private function inject()
    {
        $args = func_get_args();
        if (is_callable(end($args))) {
            $callback = array_pop($args);
        }
        $deps = [];
        $missing = false;
        foreach ($args as $arg) {
            try {
                $deps[$arg] = call_user_func(Container::get($arg));
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
    }

    private function injectAs()
    {
    }
}

