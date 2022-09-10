<?php

use Monolyth\Disclosure\{ Injector, NotFoundException, Container};

$container = new Container;
$container->register(function (&$foo, &$bar, &$baz) {
    $foo = new Demo\BasicInjection1;
    $bar = new Demo\BasicInjection2;
    $baz = new Demo\BasicInjection3;
});
$container->register(function (&$fizz) {
    $fizz = new Demo\DeepInjection;
});

/** Injector should inject requested classes */
return function () : Generator {
    $foo = new Demo\Basic;

    /** Basic::$foo is a BasicInjection1 */
    yield function () use ($foo) {
        assert($foo->foo instanceof Demo\BasicInjection1);
    };

    /** Basic::$bar is a BasicInjection2 */
    yield function () use ($foo) {
        assert($foo->bar instanceof Demo\BasicInjection2);
    };

    /** Basic::$baz is a BasicInjection3 injected via simple string */
    yield function () use ($foo) {
        assert($foo->baz instanceof Demo\BasicInjection3);
    };

    /** inject should throw an exception if the dependency is unknown */
    yield function () use ($foo) {
        $e = null;
        try {
            $foo->inject('whatever');
        } catch (NotFoundException $e) {
        }
        assert($e instanceof NotFoundException);
    };

    /** $foo->bar is the same class and instance as $foo2->bar */
    yield function () use ($foo) {
        $foo2 = new Demo\Basic;
        assert($foo->bar === $foo2->bar);
    };
};

