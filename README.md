# Disclosure
PHP8 dependency injection and service locator framework.
Most existing DI or Inversion of Control (IoC) solutions depend on extensive
configuration files to define dependencies. This sucks; Disclosure is better
and simpler (we think).

## Installation

### Composer (recommended)
```sh
composer require monolyth/disclosure
```

### Manual installation
1. Get or clone the code;
2. Register `/path/to/disclosure/src` for the namespace `Monolyth\\Disclosure\\`
   in your PSR-4 autoloader;

## Usage
Add your dependencies to a `Container` object somewhere. It often makes sense to
do this in a central file (e.g. `src/dependencies.php`), but it's also perfectly
fine to do it alongside your class definitions.

```php
<?php

use Monolyth\Disclosure\Container;

$container = new Container;
$container->register(fn (&$foo) => $foo = new Foo);
```

The container will now assosiate the `foo` key with an object of instance `Foo`.
The naming of the key is irrelevant; just remember that they must be unique.

You may also supply an array of key/value pairs to the register method; this is
useful for objects you're always going to need, e.g. an environment object.

Tell your classes what they should depend on using the `inject` method supplied
by the `Injector` trait:

```php
<?php

use Monolyth\Disclosure\Injector;

class MyClass
{
    use Injector;

    public function __construct()
    {
        $this->inject(function ($foo, $bar) {});
        // Or, alternatively:
        $this->inject('foo', 'bar');
    }
}

class Foo
{
}

$myInstance = new MyClass;
var_dump($myInstance->foo instanceof Foo); // true

```

`inject` accepts a random number of arguments, where each argument is either a
string with a depedency name, or a callable with dependency names as arguments.
Which style you use is up to your own preference.

## Injection using attributes
As of version 3.0, it is also possible to specify dependencies in PHP8
_attributes_. This is done by specifying the `Monolyth\Disclosure\Depends`
attribute on the property that should be injected. The property name should,
of course, match a registered dependency.

When specifying dependencies using attributes, you may simply call `inject`
without any arguments. You can also mix these strategies; since injected names
must be unique, it doesn't really matter.

## Instantiating using the Disclosure factory
Also new in version 3.0 is the inclusion of the `Monolyth\Disclosure\Factory`.
Objects constructed via its `build` method will automatically have their
dependencies added:

```php
<?php

use Monolyth\Disclosure\{ Depends, Factory };

class MyObject
{
    [#Depends]
    private Foo $foo;

    public function __construct($someArgument, $anotherArgument)
    {
        $this->someArgument = $someArgument;
        $this->anotherArgument = $anotherArgument;
    }

    public function doSomething()
    {
        return $this->foo->method($this->someArgument, $this->anotherArgument);
    }
}

$myobject = Factory::build(MyObject::class, 'someArgument', 'anotherArgument');
var_dump($myobject->doSomething()); // Whatever Foo::method does...
```

## Injection using promoted constructor properties
A cool new feature in PHP8 is _promoted constructor properties_. In short,
instead of writing this:

```php
<?php

class Foo
{
    private $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
        // ...other constructor stuff...
    }
}
```

...you are now allowed to write _this_:

```php
<?php

class Foo
{
    public function __construct(private Bar $bar)
    {
        // ...other constructor stuff, $this->bar is already set...
    }
}
```

And guess what? These can also be annotated! You guessed it: if you annotate a
promoted constructor property with `Depends` _and_ construct using the
`Factory::build` method, you don't even have to worry about them anymore!

```php
<?php

use Monolyth\Disclosure\{ Inject, Factory };

class Foo
{
    public function __construct(
        #[Depends]
        private Bar $bar
    ) {
    }
}
$foo = Factory::build(Foo::class);
```

Any non-promoted constructor arguments will be passed in-order from the
additional arguments given to `build`:

```php
<?php

use Monolyth\Disclosure\{ Inject, Factory };

class Foo
{
    public function __construct(
        #[Depends]
        private Bar $bar,
        string $someOtherArgument,
        #[Depends]
        public DateTime $dateTime,
        int $aNumber
    );
}
$foo = Factory::build(Foo::class, 'Hello world!', 42);
```

Note that when using promoted arguments for injection, it is _no longer_
necessary to "use" the `Injector` trait if you don't otherwise use this
strategy.

You could, in theory, also make the promoted properties nullable and _then_
call `inject` from your constructor (or anywhere else, really). But, y'know,
seriously?

## Calling a parent constructor that _also_ depends on promoted properties?
For this, Disclosure supplies the `Mother` trait with its method
`callParentConstructor`. Pass any additional (non-injected) arguments as,
ehm, arguments, and the trait will fill out the rest and inject where needed:

```php
<?php

use Monolyth\Disclosure\{ Factory, Mother, Depends };

class Foo
{
    public function __construct(
        #[Depends]
        protected SomeDependency $something,
        public int $someArgument
    ) {}
}

class Bar extends Foo
{
    use Mother;

    public function __construct(protected string $anotherArgument)
    {
        $this->callParentConstructor(42);
        echo get_class($this->something); // SomeDependency
        echo $this->someArgument; // 42
        echo $this->anotherArgument; // hello world
    }
}

$bar = Factory::build(Foo::class, 'hello world');
```

Of course, the `Mother` trait may be used regardless of whether the parent class
was instantiated using `Factory::build` or uses the `Injector` (or neither). So
this is also fine:

```php
<?php

$bar = new Bar('hello world');
```

## Resolving circular dependencies
Sometimes you will run into the sticky situation where dependencies become
_circular_. So, class A depends on an object of class B, and class B depends on
one of class A. This will cause an infinite loop and, depending on what you're
using, a fatal error, segmentation fault or just a very unhelpful blank screen.

Disclosure throws a `Monolyth\Disclosure\CircularDependencyException` when it
detects such a situation, with a message detailing the full stack that led up to
the circular dependency. You must use this message to fix your circular logic.
This exception extends PHP's built in `LogicException`.
We are working on a tool that attempts to identify these issues (as long as you

Assuming you cannot resolve the circular dependency logically (i.e., A really
needs B somewhere and vice versa), your best bet is to fall back to the
`Injector` and `inject` either or both of the offending dependencies JIT where
they are used. This will allow the objects in question to get fully
instantiated, and after that the problem should usually go away.

The alternative is to not inject the offending object as a dependency, but
rather pass or set it manually.

