<?php

namespace Monolyth\Disclosure;

/**
 * Interface resolvable classes must implement. This allows the static `resolve`
 * method to auto-resolve child classes too.
 */
interface Injectable
{
    public static function resolve() : object;
}

