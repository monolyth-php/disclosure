<?php

use disclosure\Container;
use disclosure\Injector;

require realpath(__DIR__).'/../autoload.php';

error_reporting(E_ALL);

class Foo1
{
}

class Foo2
{
}

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

