<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $namespace, string $path)
 * @method static void locate()
 * @method static array hooks()
 * @method static void loadHooks()
 * @method static string getCachePath()
 */
class Hook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MorningMedley\Hook\Hook::class;
    }
}
