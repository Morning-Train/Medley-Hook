<?php

namespace MorningMedley\Hook\Contracts;

interface Hook
{
    public function __construct(string|object $hook, int $priority = 10);

    public function register(callable $callback, int $numArgs = 1): void;
}
