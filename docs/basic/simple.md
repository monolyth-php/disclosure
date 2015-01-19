# Simple assignment

In its simplest form, you inject a variable into a class in the constructor.
Disclosure will figure out an object of this class wasn't used previously, and
will instantiate one for you:

    <?php

    use Disclosure\Injector;

    class Foo
    {
        use Injector;

        public function __construct()
        {
            $this->inject(function(Bar $bar) {});
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

Injecting works for any non-scalar value by the way (injecting scalars is...
less than useful):

    <?php

    Foo::inject(function (&$array, &$callable) {
        $array = [1, 2, 3];
        $callable = function ($what = 'world') {
            return "Hello $what!";
        };
    });

You cannot "unregister" a dependency, because really, why would you? Just don't
inject it where you don't need it.
