<?php

use Disclosure\Container;
use Disclosure\Injector;
use Disclosure\UnregisteredException;

$container = new Container;
$container->register('Bar');
$container->register('Alias', 'Baz');
$container->register('Service', new Bar);
$container->register('Service2', $container->get('Service'));
$container->register('Service3', new Bar);

class Foo
{
    use Injector;

    public function __construct(Container $container)
    {
        $this->disclosureContainer = $container;
        $this->inject('Bar', 'Alias', 'Service', 'Service2', 'Service3');
    }
}

class FooUnregistered
{
    use Injector;

    public function __construct(Container $container)
    {
        $this->disclosureContainer = $container;
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
        global $container;
        $foo = new Foo($container);
        $this->assertInstanceOf('Bar', $foo->Bar);
    }

    public function testFooWithAlias()
    {
        global $container;
        $foo = new Foo($container);
        $this->assertInstanceOf('Baz', $foo->Alias);
    }

    public function testService()
    {
        global $container;
        $foo = new Foo($container);
        $this->assertSame($foo->Service, $foo->Service2);
        $this->assertNotSame($foo->Service, $foo->Service3);
    }

    public function testUnregistered()
    {
        global $container;
        $e = null;
        try {
            $foo = new FooUnregistered($container);
        } catch (Exception $e) {
        }
        $this->assertInstanceOf('Disclosure\UnregisteredException', $e);
    }
}

