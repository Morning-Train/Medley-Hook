<?php

namespace MorningMedley\Hook\Abstracts;

use MorningMedley\Hook\Classes\CallbackManager;
use PHPUnit\TextUI\ReflectionException;

/**
 *
 */
abstract class AbstractHook
{

    protected int $priority = 10;
    protected string $hook;

    /**
     * @param  string|array  $hook  or hooks to apply to
     *
     * @param $callback
     */
    public function __construct(string $hook, int $priority = 10)
    {
        $this->hook = $hook;
        $this->priority = $priority;
    }

    /**
     * Add/register the hook into WordPress
     *
     * @return mixed
     */
    abstract public function register(callable $callback, int $numArgs = 1);
}
