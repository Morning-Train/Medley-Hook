<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;
use MorningMedley\Hook\Classes\HookCollection;

/**
 * @method static void register(string $namespace, string $path)
 * @method static void locate()
 * @method static array hooks()
 * @method static void loadHooks()
 * @method static string getCachePath()
 * @method static HookCollection hookCollection()
 */
class Hook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MorningMedley\Hook\Hook::class;
    }
}
