<?php

use Monolyth\Disclosure\{ Container, Factory };

$container = new Container;
$container->register(fn (&$foo) => $foo = new Demo\BasicInjection1);
$container->register(fn (&$bar) => $bar = new Demo\BasicInjection2);

/** Injector should inject classes requested via attributes */
return function () : Generator {
    /** Properties marked with attributes get injected */
    yield function () {
        $foo = new Demo\WithAttributes1;
        $foo->inject();
        assert(isset($foo->foo));
        assert($foo->foo instanceof Demo\BasicInjection1);
    };
    /** Promoted properties get injected when instantiating using the factory */
    yield function () {
        $foo = Factory::build(Demo\WithAttributes2::class);
        assert(isset($foo->foo));
        assert($foo->foo instanceof Demo\BasicInjection1);
    };
    /** For additional arguments, these also get passed correctly */
    yield function () {
        $foo = Factory::build(Demo\WithAttributes3::class, 'Hello world', 42);
        assert(isset($foo->foo));
        assert($foo->foo instanceof Demo\BasicInjection1);
        assert(isset($foo->bar));
        assert($foo->bar instanceof Demo\BasicInjection2);
    };
};

