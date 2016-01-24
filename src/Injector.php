<?php

namespace Disclosure;

trait Injector
{
    /**
     * @Untestable
     */
    public function inject(callable $inject)
    {
        $injections = Container::inject(__CLASS__, $inject);
        $missing = false;
        foreach ($injections as $requested => $value) {
            if (is_string($value)) {
                $missing = true;
                continue;
            }
            $this->$requested = $value;
        }
        if ($missing) {
            throw new UninjectableException;
        }
    }

    /**
     * @Untestable
     */
    public static function register(callable $inject)
    {
        Container::inject(__CLASS__, $inject);
    }
}

