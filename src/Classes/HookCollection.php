<?php

namespace MorningMedley\Hook\Classes;

use Illuminate\Contracts\Foundation\Application;

class HookCollection
{
    /**
     * @var string[]
     */
    private array $hooks = [];

    private bool $loaded = false;

    public function __construct(protected Application $app)
    {

    }

    /**
     * Add one or more classes for Hooking
     *
     * @param  string|string[]  $classes
     * @return void
     */
    public function add(string|array $classes): void
    {
        if ($this->loaded) {
            $this->loadClasses((array) $classes);
        }

        $this->hooks = [...$this->hooks, ...(array) $classes];
    }

    /**
     * Get current Hook list
     *
     * @return string[]
     */
    public function hooks(): array
    {
        return $this->hooks;
    }

    /**
     * Load and clear Hooks
     *
     * @return bool
     */
    public function load(): bool
    {
        if ($this->loaded) {
            return false;
        }

        $this->loadClasses($this->hooks());

        $this->loaded = true;

        return true;
    }

    public function loadClasses(array $classes): void
    {
        foreach ($classes as $class) {
            if (class_exists($class) && method_exists($class, 'hookClass')) {
                try {
                    $this->app->make($class)->hookClass();
                } catch (\Throwable $e) {

                }
            }
        }
    }
}
