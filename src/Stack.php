<?php

namespace Monolyth\Disclosure;

/**
 * The Stack is a tracking class used internally to catch circular dependencies.
 */
class Stack
{
    private array $items = [];

    /**
     * Push a dependency onto the stack.
     *
     * @param string $dependency
     * @return void
     * @throws Monolyth\Disclosure\CircularDependencyException
     */
    public function push(string $dependency) : void
    {
        $this->items[] = $dependency;
        if (count($this->items) != count(array_unique($this->items))) {
            $e = new CircularDependencyException($this->items);
            $this->clear();
            throw $e;
        }
    }

    /**
     * Clear (reset) the stack.
     *
     * @return void
     */
    public function clear() : void
    {
        $this->items = [];
    }
}

