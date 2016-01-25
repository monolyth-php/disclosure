# The Container object
The `Disclosure\Container` class is fully compatible with
`Psr\Container\ContainerInterface`.

## Creating a container
Just instance an object of class `Disclosure\Container`. You can do this as many
times as you like, all instances share the same "pool" of dependencies.

```php
<?php

use Disclosure\Container;

$container = new Container;
$container->register(function (&$foo) {
    $foo = new Foo;
});

$container2 = new Container;
$container2->register(function (&$bar) {
    $bar = new Bar;
});

var_dump($container->has('bar')); // true
var_dump($container2->has('foo')); // true
```

## Registering dependencies
To register a dependency, call the container's `register` method with a callable
as an argument. The parameter names are the names of the dependencies to inject.
_Specify the parameters as references!_ This is important, it is how the
container knows what you're going to assign to them.

A registering callable can have as many arguments as you like. Note that when
retrieving one of the keys in the registration, the _entire_ callable will be
evaluated. So generally it makes sense to group related dependencies into
`register` calls only. For example, a `$twig` dependency that also needs a
`TwigEnvironment` to function.

## Checking dependencies
Call the `has` method on the container with the string version of a dpendency's
name to check if it was defined. Unsurprisingly it will return `true` or
`false`, depending.

## Getting dependencies
To resolve a dependency, call the `get` method on the container with the
dependency's name as an argument:

```php
<?php

var_dump(get_class($container->get('foo'))); // "Foo"
```

If no dependency by that name was found, an instance of
`Psr\Container\Exception\NotFoundExceptionInterface` is thrown as implemented by
`Disclosure\NotFoundException`.

## Using a delegate container
Pass an instance of `Psr\Container\ContainerInterface` as a constructor
argument to the container to use a _delegate container_ instead. If an
_instance_ of `Disclosure\Container` has a delegate container, it will attempt
to resolve all dependencies on that container instead.

Delegate containers are per-instance, but Disclosure is smart enough to remember
which keys were stored on containers with a delegate.

> Note that for each depedency registered on a "delegated instance", the
> dependency of that name will be resolved using the delegate, until the key
> happens to be overwritten in which case the default container takes over
> again.

An example:

```php
<?php

$container = new Disclosure\Container(new MyHomebrewContainer);
$container->register(function (&$foo, &$bar) {
    $foo = new Foo;
    $bar = new Bar;
});
$container2 = new Disclosure\Container;
$conteiner2->register(function (&$bar) {
    $bar = new Baz;
});
```

...and in a totally different file where you need the injected dependency:

```php
<?php

$container = new Disclosure\Container;
$foo = $container->get('foo'); // Success! $foo retrieved from HomeBrewContainer
$container->get('bar') instanceof 'Bar'; // False: it's now a Baz
```

Constructing with an object that is not an instance of the `ContainerInterface`
will cause a fatal error. C'est la vie.

You can mix and match at will, as long as dependency names are unique.

> Also note that, as per the PSR spec, any dependencies on `$foo` in this
> example would be handled by the delegate. For `$bar` they would not, since it
> was overridden to use Disclosure's container instead.

## Injecting during registration
If any parameter to the injecting callable is _not_ a reference, it will be
injected as if `Container::get` had been called. This allows you to mix and
match as is convenient for your application logic.

```php
<?php

$container = new Disclosure\Container;
$container->inject(function ($foo, &$bar) {
    $bar = new Bar($foo);
});
$container->inject(function (&$foo) {
    $foo = new $foo;
});
```

The above example also illustrates that the order doesn't matter; the
dependencies aren't resolved until actually retrieved anyway.

> Obviously if we wrote `$container->get('bar')` in between the two `inject`
> calls it _would_ have failed, since Disclosure alas can't lookahead.

