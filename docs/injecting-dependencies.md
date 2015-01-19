# Injecting dependencies

To inject dependencies into consuming classes, use the `Injector` trait:

    <?php

    use Disclosure\Injector;

    class Foo
    {
        public function __construct()
        {
            $this->inject('Bar');
        }
    }

