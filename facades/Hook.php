<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;
use MorningMedley\Hook\Classes\HookCollection;

/**
 * @method static void register(string $namespace, string $path)    // Register a namespace and path containing Hookables
 * @method static void locate()                                     // Locate hooks in paths
 * @method static array hooks()                                     // Get hooks from collection
 * @method static void loadHooks()                                  // Load hooks in collection
 * @method static HookCollection hookCollection()                   // Get collection instance
 * @method static string getCachePath()                             // Get path to cache file
 * @method static bool hooksAreCached()                             // Cache file exists
 */
class Hook extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MorningMedley\Hook\Hook::class;
    }
}
