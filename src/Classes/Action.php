<?php

namespace MorningMedley\Hooks\Classes;

use Attribute;

/**
 * A WordPress action
 *
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Action extends \MorningMedley\Hooks\Abstracts\AbstractHook
{

    public function register(callable $callback, int $numArgs = 1)
    {
        \add_action($this->hook, $callback, $this->priority, $numArgs);
    }
}
