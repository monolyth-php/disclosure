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
1. Get or clonse the code;
2. Register `/path/to/disclosure/src` for the namespace `Monolyth\\Disclosure\\`
   in your PSR-4 autoloader;
3. Register `/path/to/disclosure/psr` for the namespace `Psr\\Container\\` in
   your PSR-4 autoloader

## Usage
Add your dependencies to a `Container` object somewhere. It often makes sense to
do this in a central file (e.g. `src/dependencies.php`), but it's also perfectly
fine to do it alongside your class definitions.

```php
<?php

use Monolyth\Disclosure\Container;

$container = new Container;
$container->register(function (&$foo, &$bar) {
    $foo = new Foo;
    $bar = new Bar;
});
```

The container will now assosiate the `foo` key with an object of instance `Foo`.
The naming of the key is irrelevant; just remember that they must be unique.

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

`inject` accepts a random number of arguments, where each argument is their a
string with a depedency name, or a callable with dependency names as arguments.
Which style you use is up to your own preference.

## _Whoah!_ Why not simply do `$this->foo = new MyDependency;`?
There's plenty of reasons for using a Container instead of the `new` keyword
all over the place, but the main ones are:

- Hard-coding instances makes it hard to inject mocks during unit tests (you
  could use `class_alias` for that, but seriously).
- It causes tight coupling between classes, which is a Bad Thing(tm).
- It makes it easier to inject objects as a Service locator (i.e., one instance
  of an object instead of a new one each time).

No, in the above example it doesn't add much, but see the complete documentation
for real-world, practical examples of why dependency injection is generally a
good idea.

## Injection using attributes
As of version 2.2, it is also possible to specify dependencies in PHP8
_attributes_. This is done by specifying the `Monolyth\Disclosure\Inject`
attribute on the _class_, with the required key as an argument.

> Alternatively, both when calling `inject` or adding an `Inject` attribute, you
> may simply specify the _classname_ to inject; Disclosure will use the first
> dependency of that class (or whatever extends/implements it) it finds. Take
> care to only do this for dependencies you _know_ are unique, or the results
> may be very unpredictable!

When specifying dependencies using attributes, you may simply call `inject`
without any arguments. If these strategies are _mixed_, Disclosure will _first_
inject dependencies specified by attributes, _then_ the injector arguments,
where the latter may override the former.

## Instantiating using the Disclosure factory
Also new in version 2.2 is the inclusion of the `Monolyth\Disclosure\Factory`.
Objects constructed via its `build` method will automatically have their
dependencies added:

```php
<?php

use Monolyth\Disclosure\{ Inject, Factory };

class MyObject
{
    [#Inject]
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

