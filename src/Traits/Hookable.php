<?php

namespace MorningMedley\Hooks\Traits;

use MorningMedley\Hooks\Abstracts\AbstractHook;
use Reflector;

trait Hookable
{
    /**
     * Setup hooks for a class
     *
     * @return void
     */
    public function hookClass()
    {
        foreach ($this->getHookableTargets() as $hookableReflection) {
            $attributes = $hookableReflection->getAttributes();
            if (empty($attributes)) {
                continue;
            }

            $instances = array_map(fn($a) => $a->newInstance(), $attributes);
            $hooks = array_filter($instances, fn($i) => is_a($i, AbstractHook::class));

            foreach ($hooks as $hook) {
                $this->registerHook($hook, $hookableReflection);
            }
        }
    }

    /**
     * @return Reflector[]
     */
    protected function getHookableTargets(): array
    {
        $reflection = new \ReflectionClass($this);

        return [...$reflection->getMethods(), ...$reflection->getProperties()];
    }

    /**
     * Register a Hook by its reflector
     *
     * @param  AbstractHook  $hook
     * @param  Reflector  $reflection
     *
     * @return void
     */
    protected function registerHook(AbstractHook $hook, Reflector $reflection)
    {
        switch (true) {
            case is_a($reflection, \ReflectionMethod::class):
                $hook->register([$this, $reflection->getName()],
                    $reflection->getNumberOfParameters());
                break;
            case is_a($reflection, \ReflectionProperty::class):
                $hook->register(fn() => $reflection->getValue($this));
                break;
        }
    }
}
