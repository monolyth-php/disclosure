<?php

namespace Disclosure\Test;

use Disclosure\UnregisteredException;
use Demo;

/**
 * @Description Markers should trigger injection
 */
class MarkerTest
{
    /**
     * @Description {0}::$bar is a BasicInjection
     */
    public function testMarkerInterfaceResolvesDependency(Demo\ImplementsMarker $foo)
    {
        return new Demo\BasicInjection;
    }

    /**
     * @Description {0}::$bar is deeply injected as BasicInjection
     */
    public function testMarkerInterfaceDeep(Demo\ImplementsDeepMarker $foo)
    {
        return new Demo\BasicInjection;
    }

    /**
     * @Description {0}::$bar is resolved by trait as BasicInjection
     */
    public function testTraitResolvesDependency(Demo\UsesTrait $foo)
    {
        return new Demo\BasicInjection;
    }
}

