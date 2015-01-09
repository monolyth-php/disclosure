# disclosure
PHP5 dependency injection framework

Installation
------------

1. Clone the repository
2. Include the autoloader: `<?php include '/path/to/disclosure/autoload.php' ?>`
3. Done! Start using it :)

Usage
-----

Disclosure is a dependency injection and service locator framework, so
your existing code has to be adapted to work with it. This means there
are two steps to be taken:

1. Register stuff that can be depended on;
2. Make your classes depend on stuff.

###Registering depedables###

How and where in your code you register dependables is up to you. You could
adapt your autoloader to guesstimate an external file, or include them in
your class definition file (this is safe as long as you specify any
overrides first) or do something manual if you're that way inclined.

    <?php

    use disclosure\Container;

    // Register class Foo:
    Container::register('Foo');
    // Register class Foo as bar:
    Container::register('bar', 'Foo');
    // Register Foo as a service:
    Container::register('Foo', new Foo);
    // Or, assuming the instance method returns a singleton:
    Container::register(['Foo', 'instance']);
    // Or, assuming it doesn't:
    Container::register('Foo', function() {
        static $foo;
        if (!isset($foo)) {
            $foo = new Foo;
        }
        return $foo;
    });

`Container::register` takes either one or two arguments. In the latter case,
the first argument is treated as the dependency name. The other argument
can be either:

* A classname, which will be instantiated on injection;
* An object, which will be injected verbatim;
* A callable, the return value of which will be injected.

If the dependency name is unspecified, disclosure tries to guesstimate it:

* For a classname, use the name (without namespaces);
* For a callable, when an array with class/method elements, use the classname
  as specified above;
* For a lambda, generate one and return it (this will seldom be useful, but hey,
  it's there should you need it).

PHP treats a lambda as an object, so to register a class with an `__invoke`
method (which is indistinguishable from a callback) wrap it in a callback
itself:

    <?php

    use disclosure\Container;

    class Foo
    {
        public function __invoke()
        {
        }
    }

    Container::register('Foo', function() {
        return new Foo;
    });

Failure to do so will erroneously cause disclosure to call `__invoke` and
register its return value, which is probably _not_ what you want!

You can also use `Container::get` to retrieve another registered dependency:

    <?php

    use disclosure\Container;

    Container::register('Foo');
    Container::register('bar', Container::get('Foo'));

`Container::get` returns a promise of sorts, so the order here is not important.
Just make sure Foo exists as a dependency by the time you inject it somewhere.

#### Unregistering dependencies ####

Note that any existing dependency takes prevalence, so you cannot override
one. All subsequent registrations are silently dropped. This is by design, so
that you can define a default dependency in a class definition, but override
it in some central project file without worrying about an autoloader not being
called until much later.

In the rare case that you need to manually override a dependency (but note that
this is usually a symptom of bad design!) you may use
`Container::unregister($dependencyName)` first.

###Injecting dependencies###

In your consuming class, `use` the Injector trait:

    <?php

    use disclosure\Injector;

    class Bar
    {
        use Injector;
    }

The Injector trait adds the method `inject` to your class. You'll generally want
to call this in your constructor, but this is of course up to you (you might
need to load dependencies for a single method only in that method, which is
perfectly fine).

`inject` takes a variable number of arguments, each of which should correspond
to an injectable dependency. A `disclosure\UnregisteredException` is thrown if
a dependency was not defined previously. Any other dependency _will_ be
resolved, so you may catch the exception and handle accordingly.

    <?php

    use disclosure\Injector;

    class Bar
    {
        use Injector;

        public function __construct()
        {
            // Set $this->Foo to the Foo dependency:
            $this->inject('Foo');
        }

#### Injection with callback ####

Optionally, the last argument to `inject` may be a function, which should return
a hash (key/value pairs) of injected objects. This allows for AngularJS-style
injection:

    <?php

    use disclosure\Injector;

    class Bar
    {
        use Injector;

        public function __construct()
        {
            // Set $this->bar to the Foo dependency:
            $this->inject('Foo', function($bar) { return get_defined_vars(); });
        }
    }

For convenience, the Injector trait also exposes the method `injectAs` which,
like `inject`, takes a variable number of arguments, only this time specifying
all aliases:

    <?php

    use disclosure\Injector;

    class Bar
    {
        use Injector;

        public function __construct()
        {
            // Set $this->bar to the Foo dependency:
            $this->inject('Foo', $this->injectAs('bar'));
        }
    }

#### Injection with type hinting ####

A final use of the callback-style registration is to allow for type hinting:

    <?php

    use disclosure\Injector;

    class Bar
    {
        use Injector;

        public function __construct()
        {
            // Set $this->bar to the Foo dependency:
            $this->inject('Foo', function(Foo $bar) {
                return get_defined_vars();
            });
        }
    }

This is useful in larger projects using libraries with overrides, where
you need to assure a dependency is of a certain type (interfaces are
your friend here).

Note that if the number of arguments to the callback do not match the number
of injected dependencies, a BadMethodCallException is thrown.

###Visibility###

Since `Injector::inject` works directly on `$this`, you can control visibility
in the normal way:

    <?php

    use disclosure\Injector;

    class Bar
    {
        use Injector;

        private $bar;

        public function __construct()
        {
            // Private depedency $this->bar:
            $this->inject('Foo', function(Foo $bar) {
                return get_defined_vars();
            });
        }
    }

Circular dependencies
---------------------

Having circular dependencies is usually a symptom of bad design. Consider the
following:

* class Foo depends on class Bar;
* class Bar depends on class Foo.

When disclosure detects a circular dependency, it will throw a
`disclosure\CircularDependencyException`.
