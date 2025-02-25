<?php

namespace Monolyth\Disclosure;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionFunction;

/**
 * Main container class for Disclosure. All instances share a global pool of
 * registered dependencies.
 */
class Container implements ContainerInterface
{
    private static array $map = [];

    private ?ContainerInterface $delegate;

    /**
     * Constructor. Optionally pass a delegate container to use. Note that
     * delegates are tried before the default container. IF the delegate lookup
     * fails, Disclosure will try the default before giving up.
     *
     * @param Psr\Container\ContainerInterface $delegate Optional delegate
     *  container to use.
     * @return void
     */
    public function __construct(?ContainerInterface $delegate = null)
    {
        if (isset($delegate)) {
            $this->delegate = $delegate;
        }
    }

    /**
     * Resolve the dependency identified by `$key`. Note `$key` is not type
     * hinted as it would violate the `Psr\Container\ContainerInterface`.
     *
     * @param string $key The unique identifier for the dependency.
     * @return mixed Whatever was stored under $key.
     * @throws Monolyth\Disclosure\NotFoundException if no such $key was
     *  registered.
     */
    public function get($key) : mixed
    {
        static $stack = new Stack;
        if (!array_key_exists($key, static::$map)) {
            throw new NotFoundException($key);
        }
        if (static::$map[$key] instanceof ReflectionFunction) {
            $stack->push($key);
            static::$map[$key] = static::$map[$key]->invoke($this);
            $stack->clear();
        }
        return static::$map[$key];
    }

    /**
     * Checks if a dependency exists identified by $key. Note that this also
     * resolves the dependency internally, so any dependencies on $key must also
     * be resolvable for this to return true.
     *
     * Note `$key` is not type hinted as it would violate the
     * `Psr\Container\ContainerInterface`.
     *
     * @param string $key The unique identifier for the dependency.
     * @return bool True if $key identifies a known dependency, else false.
     */
    public function has($key) : bool
    {
        try {
            $this->get($key);
            return true;
        } catch (NotFoundExceptionInterface $e) {
            return false;
        }
    }

    /**
     * Register a group of dependencies defined in the callable. Each referenced
     * argument should be assigned its designated value when invoked. Note that
     * invocation takes place only when a dependency is retrieved.
     *
     * @param callable|array $inject A callable or array associating values with keys.
     * @return void
     */
    public function register(callable|array $inject) : void
    {
        if (is_array($inject)) {
            self::$map += $inject;
            return;
        }
        $reflection = new ReflectionFunction($inject);
        $parameters = $reflection->getParameters();
        foreach ($parameters as $parameter) {
            $key = $parameter->name;
            $getter = function ($c) use ($reflection, $parameters, $key) {
                if (isset($c->delegate)) {
                    try {
                        return $c->delegate->get($key);
                    } catch (NotFoundExceptionInterface $e) {
                        // That's fine, we'll try our own container next.
                    }
                }
                $args = [];
                foreach ($parameters as $param) {
                    if (!$param->isPassedByReference()) {
                        $args[] = $c->get($param->name);
                    } else {
                        ${$param->name} = null;
                        $args[$param->name] =& ${$param->name};
                    }
                }
                $reflection->invokeArgs($args);
                foreach ($args as $found => $value) {
                    if (!is_numeric($found) && $found == $key) {
                        $c::$map[$found] = $value;
                    }
                }
                if (array_key_exists($key, $args)) {
                    return $args[$key];
                }
                throw new NotFoundException($key);
            };
            static::$map[$key] = new ReflectionFunction($getter);
        }
    }
}

