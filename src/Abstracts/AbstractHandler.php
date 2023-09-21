<?php

namespace Morningtrain\WP\Hooks\Abstracts;

abstract class AbstractHandler
{
    public function __construct()
    {
        $r = new \ReflectionClass($this);
        $methods = $r->getMethods();
        foreach ($methods as $method) {
            if (empty($method->getAttributes())) {
                continue;
            }
            $attributes = $method->getAttributes();
            $instances = array_map(fn($a) => $a->newInstance(), $attributes);
            $hooks = array_filter($instances, fn($i) => is_a($i, AbstractHook::class));
            foreach ($hooks as $hook) {
                $hook->register([$this, $method->getName()], $method->getNumberOfParameters());
            }

        }
    }
}
