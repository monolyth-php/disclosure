<?php

use Disclosure\Container;
use Disclosure\Injector;

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
}

/*
Container::register('Foo1');
Container::register(new Foo2);
Container::register('Foo3', new Foo2);
Container::register('Foo4', function() {
    static $foo4;
    if (!isset($foo4)) {
        $foo4 = new Foo1;
    }
    return $foo4;
});
Container::register('Foo5', Container::get('Foo4'));

class Bar
{
    use Injector;

    public function __construct()
    {
        $this->inject('Foo1', 'Foo2', 'Foo3', 'Foo4', 'Foo5');
    }
}

class Baz
{
    use Injector;

    public function __construct()
    {
        $this->inject('Foo4', function($foobar) {
            return get_defined_vars();
        });
    }
}

$bar = new Bar;
var_dump($bar->Foo1);
var_dump($bar->Foo2);
var_dump($bar->Foo2 === $bar->Foo3);
var_dump($bar->Foo4 === $bar->Foo5);
$baz = new Baz;
var_dump($bar->Foo4 == $baz->foobar);
*/

