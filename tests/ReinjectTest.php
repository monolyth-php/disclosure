<?php

use Disclosure\Container;

require_once __DIR__.'/classes/test.php';

class ReinjectTest extends PHPUnit_Framework_TestCase
{
    public function testReinject()
    {
        Reinjectme::inject(function (&$bar) {
            $bar = new ArgsObject(1);
        });
        $foo = new Reinjectme;
        $foo2 = new Reinjectme;
        $this->assertTrue($foo->bar == $foo2->bar);
        $this->assertNotTrue($foo->bar === $foo2->bar);
    }

    public function testGlobal()
    {
        Container::inject('*', function (&$foo) {
            $foo = new BasicInjection;
        });
        $foo = new Reinjectme;
        $foo->inject(function ($foo) {});
        $this->assertInstanceOf('BasicInjection', $foo->foo);
    }
}

