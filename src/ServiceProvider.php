<?php

namespace MorningMedley\Hook;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Hook\Classes\HookLoader;
use MorningMedley\Hook\Classes\HookLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Cache\ItemInterface;

class ServiceProvider extends IlluminateServiceProvider
{
    private string $cacheKey = 'hooks';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'hook');
    }

    public function boot(): void
    {
        $cache = $this->app->make('file.cache');
        if (\wp_get_environment_type() !== 'production') {
            $classes = $cache->get($this->cacheKey, function (ItemInterface $item) {
                $item->expiresAfter(DAY_IN_SECONDS * 30);

                return $this->findClasses();
            });
        } else {
            $classes = $this->findClasses();
        }

        foreach ($classes as $class) {
            if (class_exists($class)) {
                try {
                    new $class();
                } catch (\Throwable $e) {

                }
            }
        }
    }

    private function findClasses(): array
    {
        $paths = $this->app->get('config')->get('hook.paths');
        $classes = [];
        foreach ($paths as $namespace => $path) {
            $locator = $this->app->make(HookLocator::class);
            $classes = [...$classes, ...$locator->locate($path, $namespace, $this->app->make(Finder::class))];
        }

        return $classes;
    }
}
