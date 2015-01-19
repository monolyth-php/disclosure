# Advance declaration

Sometimes, you need to declare a dependency in advance. A common use case for
this is with some database adapter, which will typically require parameters to
instantiate. But really it probably goes for any object requiring parameters in
its constructor, since you'll want to specify them in one place only.

Let's say we have a `Model` class that depends on some `PDO` object initialized
with your database credentials:

    <?php

    use Disclosure\Injector;

    class Model
    {
        use Injector;

        public function __construct()
        {
            $this->inject(function (PDO $db) {});
        }
    }

Without further configuration, this would give an error, since a `PDO` instance
must be constructed with credentials. One way around this is to declare the
dependency "in advance" by calling `inject` statically:

    <?php

    class Model
    {
        // ...same as before...
    }

    Model::inject(function(&$db) {
        $db = new PDO('dsn', 'user', 'password');
    });

Note a number of things here:

- We dropped type hinting. We aren't declaring `$db` until we are _inside_ the
    closure; the `$db` argument passed is a reference to the future dependency.
    Hence, the type is at that point unknown by design.
- The `$db` argument is passed by reference. _This is crucial_ because inside
    the closure we're actually _changing_ `$db`. The alternative would be to
    force you to return something from the closure in this case, which is clunky
    and clumsy.

Now, when a `new Model` is instantiated, Disclosure will see there is a known
parameter `$db` with a compatible type, and inject that instead.

## Mocking services

During testing, you might not want to use an actual `PDO` instance. I trust we
don't have to explain why, but for those that are new to testing: a test almost
certainly should not change your database.

Assuming you're using PHPUnit, you could now write the following:

    <?php

    Model::inject(function(&$db) {
        $db = new MockPDO;
    });

    class ModelTest
    {
        public function testSomething()
        {
            $model = new Model;
            $this->assertInstanceOf('MockPDO', $model->db);
        }
    }

Of course, since the `Model` constructor typehints the `$db` dependency, it's up
to you to make sure `MockPDO` is compatible with that. But then again, it makes
sense for a mock to implement the same interfaces as the real class.

Hence, a more complete real world example could look like this:

    <?php

    interface Adapter
    {
        //...optional method declarations...
    }

    class MyPDO extends PDO implements Adapter
    {
        //...optional custom stuff...
    }

    class MockPDO implements Adapter
    {
        //...mocked methods...
    }

    Model::inject(function (&$db) {
        $db = new PDO('dsn', 'user', 'pass');
    });

    class Model
    {
        use Disclosure\Injector;

        public function __construct()
        {
            $this->inject(function(Adapter $db) {});
        }
    }

    //...normal usage of model:
    $model = new Model;
    $model->db('sql');

    //...testing model:
    Model::inject(function(&$db) {
        $db = new MockPDO;
    });
