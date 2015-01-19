# Registering dependencies

To register dependencies, first get an instance of the `Container` class:

    <?php

    use Disclosure\Container;

    $container = Container::instance();

## Direct assignment

The simplest way to register a dependency is by calling `Container::register`
with the classname as an argument:

    <?php

    $container->register('Foo');
    $container->get('Foo'); // instance of class Foo

To use Disclosure as a service locater, pass an instance directly instead:

    <?php

    $container->register(new Foo);
    $container->get('Foo'); // the instance of Foo instantiated earlier

If the classname or class the object is an instance of, the classname minus
namespaces will be used as the name of the dependency.

## Using aliases

You can also use two arguments; the first is now an alias:

    <?php

    $container->register('bar', 'Foo');
    $container->get('bar'); // instance of class Foo

Or pass a callable, which will first get resolved:

    <?php

    $container->register('bar', function() { return 'baz'; });
    $container->get('bar'); // string 'baz'

If a dependency of the same name already exists, the assignment is silently
discarded. This is to make it easy to define defaults in library classes
consuming dependencies:

    <?php

    $container->register('Foo');
    $container->register('Foo', 'Bar');
    $container->get('Foo'); // instance of class Foo

You should define you project dependencies first (e.g. from some bootstrap
file), and any library using Disclosure can now safely assign its own defaults
to prevent any `UnregisteredDependencyException` errors getting thrown:

    <?php

    // In your bootstrap...
    $container->register('foo', new Bar);

    // In some library...
    $container->register('foo', new Foo);
    class Baz
    {
        use Injector;

        public function __construct()
        {
            $this->inject('foo');
            // $this->foo is now a Bar
        }
    }

