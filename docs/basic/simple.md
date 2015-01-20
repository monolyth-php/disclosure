# Simple assignment

In its simplest form, you inject a variable into a class in the constructor.
Disclosure will figure out whether an object of this class was used previously
on the calling class, and if not will instantiate one for you:

    <?php

    use Disclosure\Injector;

    class Foo
    {
        use Injector;

        public function __construct()
        {
            $this->inject(function(Bar $bar) {});
            // $this->bar is now a Bar.
        }
    }

You can inject an arbitrary number of dependencies, and call `inject` an
arbitrary number of times in arbitrary places. A real world example would be
some model method that only requires a `Mail` class when a certain property has
been updated and saved.

As `inject` works directly on `$this`, it is trivial to control visibility of
the injected properties:

    <?php

    class Foo
    {

        //...same as before...

        private $bar;
    }

    $foo = new Foo;
    echo $foo->bar; // Error: $bar is a private property.

You cannot "unregister" a dependency, because really, why would you? Just don't
inject it where you don't need it.
