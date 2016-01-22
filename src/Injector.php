<?php

namespace Disclosure;

trait Injector
{
    /**
     * @Untestable
     */
    public function inject(callable $inject)
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
                $this->$requested = $value;
            }
        }
        if ($missing) {
            throw new UninjectableException;
        }
    }
}

