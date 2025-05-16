<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static null add(array $hook)
 */
class Hook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MorningMedley\Hook\Hook::class;
    }
}
