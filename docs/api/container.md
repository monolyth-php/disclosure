# Container

`Disclosure\Container` is a static class at the heart of Disclosure. It is
responsible for tracking and resolving all your dependencies.

Normally, consuming classes should `use` the `Injector` trait and not access
this interface directly.

## `Container::inject`

Publicly visible injection method. It takes two arguments:

- `@string $class` The fully qualified classname, interface or trait to inject
                   for.
- `@callable $inject` The callable doing the injecting.

> `$class` may have the special value `"*"` signifying "injectable for all
> classes that ask for these". In fact, that's practically the only time you'll
> want to call `Container::inject` directly.
>
> Another use might be if you're defining all your dependencies in a central
> place, although you might as well call `inject` statically on the consuming
> classes directly for clarity. Disclosure needs to autoload them anyway to
> determine class inheritage, implemented interfaces and traits used.

`$inject` is a callable with referenced variables. Since calling `inject`
directly is only useful from an injection definition point of view, we are going
to define the variables inside the callable. See `Injector::inject` for more
information on this.

The callable should be defined in the following format:

    <?php

    function(&$arg1, &$arg2[, ...]) {
        $arg1 = new SomeClass;
        $arg2 = new SomeOtherClass;
    };

Passing lambdas or closures here is usually what you'll do, but it could be
anything callable that accepts an argument list of references and assigns
objects to them.

`Container::inject` can only be used to inject objects, since injecting anything
else really has little to do with DI and could be solved using config files or
something like that.
