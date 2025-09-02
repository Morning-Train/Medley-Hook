<?php

namespace MorningMedley\Hook\Classes;

use Illuminate\Contracts\Foundation\Application;

class HookCollection
{
    /**
     * @var string[]
     */
    private array $hooks = [];

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
     * Empty the current Hook list
     *
     * @return void
     */
    public function clear(): void
    {
        $this->hooks = [];
    }

    /**
     * Load and clear Hooks
     *
     * @return void
     */
    public function load(): void
    {
        foreach ($this->hooks() as $class) {
            if (class_exists($class) && method_exists($class, 'hookClass')) {
                try {
                    $this->app->make($class)->hookClass();
                } catch (\Throwable $e) {

                }
            }
        }
        $this->clear();
    }
}
