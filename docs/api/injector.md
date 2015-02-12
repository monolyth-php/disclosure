#  Injector

The Injector trait is what you'll usually be using for your dependency
management.

    <?php

    use Disclosure\Injector;

    class MyDependentClass
    {
        use Injector;
        
        public function __construct()
        {
            $this->inject(function(Foo $foo, Bar $bar) {});
            $this->foo instanceof Foo; // true
            $this->bar instanceof Bar; // true
        }
    }

The trait exposes a single method: `inject`, which takes a single argument:
a callable defining your dependencies.

## Injectable

The Injectable interface can be implemented by classes to signal they support
Disclosure injection. The support can be via the standard Injector trait, or via
a custom implementation. The implementing of Injectable can be used as a marker
in your code, but Disclosure itself does not enforce it.

## `Injector::inject`

There are two ways to use `inject`:

### As class method on an instance

This is the use shown above. For _this_ instance of `MyDependantClass`, inject
a `Foo` object in member `$foo` and a `Bar` object in member `$bar`.

> Note that the type hint and member name are in no way related. Disclosure uses
> member names internally to resolve non-type hinted dependencies, so it makes
> sense to be at least a little consistent, but in essence it usually should
> make little difference.

Sometimes you'll _really_ need a new instance of a given dependency. Simply
return `true` from the closure to reinject either a new instance or a clone of
the original dependency (Disclosure will figure out what is best here).

    <?php

    use Disclosure\Injector;

    class MyDependentClass
    {
        use Injector;
        
        public function __construct()
        {
            $this->inject(function(Foo $foo, Bar $bar) {});
            $this->inject(function(Bar $buz) {});
            $this->inject(function(Foo $baz) { return true; });
            $this->bar === $this->buz; // true
            $this->foo === $this->baz; // false
        }
    }

### Statically on an implementing class

In order to be able to predefine dependencies, you can also call `inject`
statically. Assuming the class definition of `MyDependantClass` from above:

    <?php

    MyDepdendentClass::inject(function(&$foo, &$bar) {
        $foo = new Foo;
        $bar = new Bar;
    });

Static injections must _not_ contain type hinting. This is logical since we're
not defining the dependency until _inside_ the closure. Also for the same
reason we pass all arguments as references since we're going to assign them.

> The return value of the callable is discarded when called statically, for the
> simple reason that it doesn't matter: when reinjecting Disclosure has to
> decide how to handle the assignment to `$this`, but static calls don't have a
> `$this` yet.

## Shorthand: injecting with known variable names

Assume you've defined a dependency like so:

    <?php

    MyDependentClass::inject(function(&$foo) {
        $foo = new Foo;
    });

...then the use of type hinting when resolving to an instance is _optional_.
The main benefit of using it is to enforce types, but usually you'll probably
be doing that using shared base classes or interfaces anyway (so you can mock
them in tests). Hence, assuming `$this` is an instance of `MyDependentClass`,
this would work as expected:

    <?php

    $this->inject(function($foo) {});

This gives potential conflicts which are resolved on a "first matched, first
served" basis. For your own sanity also, it is advised to employ some
consistency in your variable naming.
