<?php

namespace MorningMedley\Hook\Traits;

use MorningMedley\Hook\Contracts\Hook as HookContract;
use Reflector;

trait Hookable
{
    protected bool $isHooked = false;

    /**
     * Setup hooks for a class
     *
     * @return void
     */
    public function hookClass(): void
    {
        if ($this->isHooked) {
            return;
        }

        foreach ($this->getHookableTargets() as $hookableReflection) {
            $attributes = $hookableReflection->getAttributes();
            if (empty($attributes)) {
                continue;
            }

            $instances = array_map(fn($a) => $a->newInstance(), $attributes);
            $hooks = array_filter($instances, fn($i) => is_a($i, HookContract::class));

            foreach ($hooks as $hook) {
                $this->registerHook($hook, $hookableReflection);
            }
        }

        $this->isHooked = true;
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
     * @param  HookContract  $hook
     * @param  Reflector  $reflection
     *
     * @return void
     */
    protected function registerHook(HookContract $hook, Reflector $reflection): void
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
