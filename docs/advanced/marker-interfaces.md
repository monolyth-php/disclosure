# Using marker interfaces

An Interface is of course a "contract" between a class and its implementation.
We won't go into full details here, but say we have the following interface
defined:

    <?php

    interface Foo
    {
        public function bar();
    }

Any class that "implements" the `Foo` interface is now guaranteed to implement a
public method `bar` without arguments.

"Marker interfaces" are simply empty interfaces that are used to deduce other
characteristics by for classes that implement them. An example use might be with
an MVC controller:

    <?php

    interface RequiresLogin
    {
    }

    class UpdatePasswordController implements RequiresLogin
    {
    }

Now, wherever you instantiate the correct controller, you could generically
check for interfaces it implements and, if one of them is `RequiresLogin` and
the current visitor is not an authenticated user, redirect to a login page
automatically.

Disclosure also supports marker interfaces for dependencies. In fact, you'll be
using the same static method on `Container` as `Injector::inject` does
internally:

    <?php

    interface UsesDatabase
    {
    }

    Disclosure\Container::inject('UsesDatabase', function(&$db) {
        $db = new PDO('dsn', 'user', 'pass');
    });

    class MyModel implements UsesDatabase
    {
        use Disclosure\Injector;

        public function __construct()
        {
            $this->inject(function ($db) {});
        }
    }

    class MyOtherModel implements UsesDatabase
    {
        use Disclosure\Injector;

        public function __construct()
        {
            $this->inject(function ($db) {});
        }
    }

This also works for marker interfaces extending other marker interfaces, by the
way. And of course you're free to mix and match "normal" interfaces with marker
interfaces; as far as Disclosure and PHP are concerned, an interface is an
interface. In fact, it also works for any traits the class `use`s! That last one
is particularly handy when a trait itself has a dependency.
