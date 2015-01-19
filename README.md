# disclosure
PHP5 dependency injection and service locator framework.
Most existing DI or Inversion of Control (IoC) solutions depend on extensive
configuration files to define dependencies. This sucks; Disclosure is better
and simpler (we think).

Full documentation: http://disclosure.readthedocs.org/en/latest/

## Installation

### Composer (recommended)

Add "monomelodies/disclosure" to your `composer.json` requirements:

    {
        "require": {
            "monomelodies/disclosure": ">=0.1"
        }
    }

### Manual installation

1. Get the code;
    1.1. Clone the repository, e.g. from GitHub;
    1.2. Download the ZIP (e.g. from Github) and extract.
2. Make your project recognize Reroute:
    2.1. Register `/path/to/reroute/src` for the namespace `Reroute\\` in your
        PSR-4 autoloader (recommended);
    2.2. Alternatively, manually `include` the files you need.

## Usage

Tell your classes what they should depend on:

    <?php

    use Disclosure\Injector;

    class MyClass
    {
        use Injector;

        public function __construct()
        {
            $this->inject(function (MyDependency $foo) {});
        }
    }

    class MyDependency
    {
    }

    $myInstance = new MyClass;
    var_dump($myInstance->foo);

Or define the dependency statically somewhere:

    <?php

    MyClass::inject(function (&$foo) {
        $foo = new MyDependency;
    });
    

Disclosure is a dependency injection and service locator framework, so
your existing code has to be adapted to work with it. This means there
are two steps to be taken:

1. Somewhere central in your application, register stuff that can be
    depended on;
2. Make your classes depend on stuff.

### Registering depedencies

Register dependencies using the `Container` singleton with the `register`
method. `Container::register` takes either one or two arguments. In the latter
case, the first argument is treated as the dependency name.

    <?php

    use Disclosure\Container;

    $container = Container::instance();
    $container->register('Foo');

The last argument can be either:

* A classname, which will be instantiated on injection;
* An object, which will be injected verbatim;
* A callable, the return value of which will be injected.

See the documentation for all possible uses.

### Inject dependencies in consuming classes

Use the `Disclosure\Injector` trait, and call the `inject` method it supplies:

    <?php

    use Disclosure\Injector;

    class Foo
    {
        use Injector;

        public function __construct()
        {
            $this->inject('Foo');
        }
    }

### Retrieving dependencies

You can also use `Container::get` to retrieve another registered dependency:

    <?php

    // ...assuming $container is an instance of Disclosure\Container...
    $container->register('Foo');
    $container->register('bar', $container->get('Foo'));

`Container::get` returns a promise of sorts, so the order here is not important.
Just make sure Foo exists as a dependency by the time you inject it somewhere.

### Unregistering dependencies

Note that any existing dependency takes prevalence, so you cannot override
one. All subsequent registrations are silently dropped. This is by design, so
that you can define a default dependency in a class definition, but override
it in some central project file without worrying about an autoloader not being
called until much later.

In the rare case that you need to manually override a dependency (but note that
this is usually a symptom of bad design!) you may use
`Container::unregister($dependencyName)` first.

## Circular dependencies

Having circular dependencies is usually a symptom of bad design. Consider the
following:

* class Foo depends on class Bar;
* class Bar depends on class Foo.

When Disclosure detects a circular dependency, it will throw a
`Disclosure\CircularDependencyException`.
