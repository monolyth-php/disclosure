<?php

use Disclosure\Container;
use Disclosure\Injector;
use Disclosure\UnregisteredException;

$container = Container::instance();
$container->register('Bar');
$container->register('Alias', 'Baz');
$container->register('Service', new Bar);
$container->register('Service2', $container->get('Service'));
$container->register('Service3', new Bar);

class Foo
{
    use Injector;

    public function __construct()
    {
        $this->inject('Bar', 'Alias', 'Service', 'Service2', 'Service3');
    }
}

class FooUnregistered
{
    use Injector;

    public function __construct()
    {
        $this->inject('SomethingUnregistered');
    }
}

class Bar
{
}

class Baz
{
}

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testFooHasBar()
    {
        $foo = new Foo;
        $this->assertInstanceOf('Bar', $foo->Bar);
    }

    public function testFooWithAlias()
    {
        $foo = new Foo;
        $this->assertInstanceOf('Baz', $foo->Alias);
    }

    public function testService()
    {
        $foo = new Foo;
        $this->assertSame($foo->Service, $foo->Service2);
        $this->assertNotSame($foo->Service, $foo->Service3);
    }

    public function testUnregistered()
    {
        $e = null;
        try {
            $foo = new FooUnregistered;
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('Disclosure\UnregisteredException', $e);
    }
}

