<?php

namespace MorningMedley\Hook\Classes;

use Attribute;

/**
 * Register the filter(s)
 *
 * @see https://developer.wordpress.org/reference/functions/add_filter/
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Filter extends \MorningMedley\Hook\Abstracts\AbstractHook
{

    public function register(callable $callback, int $numArgs = 1)
    {
        \add_filter($this->hook, $callback, $this->priority, $numArgs);
    }
}
