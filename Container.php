<?php

namespace disclosure;

class Container
{
    private static $registered = [];

    public static function register()
    {
    }

    public static function unregister($name)
    {
        unset(self::$registered[$name]);
    }

    public static function get($name)
    {
        return function() {
            return self::$registered[$name];
        };
    }
}

