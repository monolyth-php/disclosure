<?php

use Monolyth\Disclosure\{ Container, Factory, Depends, CircularDependencyException };

class A
{
    public function __construct(
        #[Depends]
        private B $b
    ) {}
}

class B
{
    public function __construct(
        #[Depends]
        private C $c
    ) {}
}

class C
{
    public function __construct(
        #[Depends]
        private A $a
    ) {}
}

/** Test circular dependency detection */
return function () : Generator {
    /** A circular dependency should throw an exception */
    yield function () : void {
        $e = null;
        try {
            $container = new Container;
            $container->register(fn (&$a) => $a = Factory::build(A::class));
            $container->register(fn (&$b) => $b = Factory::build(B::class));
            $container->register(fn (&$c) => $c = Factory::build(C::class));
            $a = Factory::build(A::class);
        } catch (CircularDependencyException $e) {
        }
        assert($e instanceof CircularDependencyException);
        assert($e->getMessage() === "Stack trace: b -> c -> a -> b");
    };
};

