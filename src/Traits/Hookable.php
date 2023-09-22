<?php

    namespace MorningMedley\Hooks\Traits;

    use MorningMedley\Hooks\Abstracts\AbstractHook;

    trait Hookable
    {
        public function hookMethods()
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
