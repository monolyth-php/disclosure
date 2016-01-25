# Injecting constructor dependencies
Some people prefer injecting dependencies as constructor arguments. Luckily for
them Disclosure supports this too.

The `Injector` trait also defines a static `resolve` method. Calling this on a
class returns an instance with the constructor dependencies automatically
passed, just as if you would do `new Foo($container->get('bar'))`:

```php
<?php

$container = new Disclosure\Container;
$container->register(function (&$bar) {
    $bar = new stdClass;
});

class Foo
{
    use Disclosure\Injector;

    public function __construct($bar)
    {
        $this->bar = $bar;
    }
}

$foo = Foo::resolve();
var_dump(get_class($foo->bar)); // "stdClass"
```

If the dependency is unknown _but_ it defines a type hint, a new instance of the
requested class is injected instead. Of course this will fail if the type hint
is an interface or abstract class.

## Marking classes as resolvable
For each auto-resolved class implementing `Disclosure\Injectable`, the resolver
calls `resolve` too on injected dependencies.

```php
<?php

class Foo
{
    use Disclosure\Injector;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}

$foo = Foo::resolve();
var_dump(get_class($foo->bar)); // "Bar"
```

Now, assuming `Bar` was declared as follows:

```php
<?php

class Bar implements Disclosure\Injectable
{
    use Disclosure\Injector;

    public function __construct(Baz $baz)
    {
        $this->baz = $baz;
    }
}
```

...the following will also work:
```
<?php

$foo = Foo::resolve();
var_dump(get_class($foo->bar->baz)); // "Baz"
```
