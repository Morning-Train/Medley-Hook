<?php

namespace Morningtrain\WP\Hooks\Abstracts;

use Morningtrain\WP\Hooks\Traits\Hookable;

abstract class AbstractHooks
{
    use Hookable;

    public function __construct()
    {
        $this->hookMethods();
    }
}
