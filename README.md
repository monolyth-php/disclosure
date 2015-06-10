# Disclosure
PHP5 dependency injection and service locator framework.
Most existing DI or Inversion of Control (IoC) solutions depend on extensive
configuration files to define dependencies. This sucks; Disclosure is better
and simpler (we think).

[Full documentation](http://disclosure.monomelodies.nl/docs/)

## Installation

### Composer (recommended)
Add "monomelodies/disclosure" to your `composer.json` requirements:

    {
        "require": {
            "monomelodies/disclosure": "^1.0.0"
        }
    }

### Manual installation
1. Get the code;
    1. Clone the repository, e.g. from GitHub;
    2. Download the ZIP (e.g. from Github) and extract.
2. Make your project recognize Reroute:
    1. Register `/path/to/reroute/src` for the namespace `Reroute\\` in your
       PSR-4 autoloader (recommended);
    2. Alternatively, manually `include` the files you need.

## Usage
Tell your classes what they should depend on using in `inject` method supplied
by the `Injector` trait:

```php
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
var_dump($myInstance->foo instanceof MyDependency); // true

```

For a list of full examples including type hinding, marker interfaces,
inheritance and more, see the official documentation.

_Whoah!_ Why not simply do `$this->foo = new MyDependency;` in the constructor?

For a number of reasons:

- `MyDependency` could just be an interface;
- `$foo` could be previously resolved with a subclass or mock of `MyDependency`;
- In normal usage, there is only one `$foo` instance which this enforces without
    having to resort to all kinds of Singleton mockery;
- The injecting closure can perform operations on `$foo`;
- There is now no tight coupling;
- The closure can be anything callable, including regular functions or class
    methods, which - if you like that - can be defined in an external file;
- Direct assignment causes tight coupling.

Yes, in the above example it doesn't add much, but see the complete
documentation for real-world, practical examples of why dependency injection
is generally a good idea.

