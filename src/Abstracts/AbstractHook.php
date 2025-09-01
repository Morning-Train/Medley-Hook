<?php

namespace MorningMedley\Hook\Abstracts;

use MorningMedley\Hook\Classes\CallbackManager;
use MorningMedley\Hook\Contracts\Hook;
use PHPUnit\TextUI\ReflectionException;

/**
 *
 */
abstract class AbstractHook implements Hook
{

    protected int $priority = 10;
    protected string $hook;

    /**
     * @param  string|object  $hook  or hooks to apply to
     *
     * @param $callback
     */
    public function __construct(string|object $hook, int $priority = 10)
    {
        // Intended here for allowing StringBacked Enums
        if (is_object($hook) && is_string($hook?->value)) {
            $hook = $hook->value;
        }

        $this->hook = $hook;
        $this->priority = $priority;
    }

    /**
     * Add/register the hook into WordPress
     */
    abstract public function register(callable $callback, int $numArgs = 1): void;
}
