<?php

use Disclosure\Container;

require_once __DIR__.'/classes/test.php';

class SharingTest extends PHPUnit_Framework_TestCase
{
    public function testEquality()
    {
        $foo = new Basic;
        $foo2 = new Basic2;
        $this->assertTrue($foo->bar == $foo2->bar);
        $this->assertNotTrue($foo->bar === $foo2->bar);
    }
}

