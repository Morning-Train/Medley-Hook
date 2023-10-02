<?php

namespace MorningMedley\Hook\Abstracts;

use MorningMedley\Hook\Traits\Hookable;

abstract class AbstractHooks
{
    use Hookable;

    public function __construct()
    {
        $this->hookClass();
    }
}
