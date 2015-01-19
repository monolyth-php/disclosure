<?php

use Disclosure\Container;
use Disclosure\UnregisteredException;

require_once __DIR__.'/classes/test.php';

class MarkerTest extends PHPUnit_Framework_TestCase
{
    public function testMarkerInterfaceResolvesDependency()
    {
        Container::inject('Marker', function(&$bar) {
            $bar = new BasicInjection;
        });
        $foo = new ImplementsMarker;
        $this->assertInstanceOf('BasicInjection', $foo->bar);
    }

    public function testMarkerInterfaceDeep()
    {
        Container::inject('Marker', function(&$bar) {
            $bar = new BasicInjection;
        });
        $foo = new ImplementsDeepMarker;
        $this->assertInstanceOf('BasicInjection', $foo->bar);
    }

    public function testTraitResolvesDependency()
    {
        Container::inject('TraitDependency', function(&$bar) {
            $bar = new BasicInjection;
        });
        $foo = new UsesTrait;
        $this->assertInstanceOf('BasicInjection', $foo->bar);
    }
}

