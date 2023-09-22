<?php

namespace Morningtrain\WP\Hooks\Classes;

use Attribute;

/**
 * Register the filter(s)
 *
 * @see https://developer.wordpress.org/reference/functions/add_filter/
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Filter extends \Morningtrain\WP\Hooks\Abstracts\AbstractHook
{

    public function register(callable $callback, int $numArgs)
    {

        \add_filter($this->hook, $callback, $this->priority, $numArgs);

    }
}
