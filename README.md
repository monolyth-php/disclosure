# Disclosure
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

Tell your classes what they should depend on using in `inject` method supplied
by the `Injector` trait:

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

For a list of full examples including type hinding, marker interfaces,
inheritance and more, see the official documentation.
