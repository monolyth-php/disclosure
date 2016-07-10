# The Injector trait
While you could use calls to `Container::get` throughout your code, Disclosre
also offers a more convenient way of injecting dependencies: the `Injector`
trait.

## Usage
`use` the trait in your class, and call the `inject` method it provides whenever
required. `inject`, like `Container::register`, expects a callable where the
parameter names are the keys to inject. They will then be placed on properties
of the same names on your object:

```php
<?php

use Monolyth\Disclosure\Injector;

class Foo
{
    use Injector;

    public function __construct()
    {
        $this->inject('bar');
        // Assuming 'bar' was registered as a 'new Bar':
        $this->bar instanceof Bar; // true
    }
}
```

Essentially, this just calls `Container::get` for each parameter and assigns the
resolved dependency to `$this->$parameter`.

## Class-based registration
Classes implementing the `Injector` trait can also directly register
dependencies by using the provided `register` method. This works identical to
the `Container::register` method, but saves you the hassle of importing another
class and creating an instance:

```
<?php

// Assuming Foo from before...
Foo::register(function (&$baz) {
    $baz = new Baz;
});
```

Note that registering "on a class" in itself has no special meaning, _but_ it
has two advantages:

1. The dependencies aren't set until the last minute, which could be a
   performance consideration;
2. Since keys are assumed to be unique, you _could_ leverage this to re-use a
   key with a different resolution for certain classes.

Regarding the second point, take care: assuming a class `A` with an injected
dependency `Y` under key `$foo`, and a class `B` autoloaded subsequently with a
dependency `Z` also under key `$foo`, the instances of `A` and `B` will behave
as expected when autoloaded. However, if you then construct a _second_ instance
of `A`, it will use the overwritten dependency actually belonging to `B`.

> In general, it's best to just make sure dependency names are unique.

## Constructor-based injection

