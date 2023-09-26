<?php

namespace MorningMedley\Hooks\Abstracts;

use MorningMedley\Hooks\Traits\Hookable;

abstract class AbstractHooks
{
    use Hookable;

    public function __construct()
    {
        $this->hookMethods();
        $this->hookProperties();
    }
}
