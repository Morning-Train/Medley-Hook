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
        if ($this->app->isProduction()) {
            $cache = $this->app->make('filecachemanager')->getCache('hook');
            $classes = $cache->get($this->cacheKey, function (ItemInterface $item) {
                return $this->findClasses();
            });
        } else {
            $classes = $this->findClasses();
        }

        foreach ($classes as $class) {
            if (class_exists($class) && method_exists($class, 'hookClass')) {
                try {
                    $this->app->make($class)->hookClass();
                } catch (\Throwable $e) {

                }
            }
        }
    }

    private function findClasses(): array
    {
        $paths = $this->app->make('config')->get('hook.paths');
        $classes = [];
        foreach ($paths as $hookPath) {
            [$namespace, $path] = $hookPath;
            $locator = $this->app->make(HookLocator::class);
            $classes = [...$classes, ...$locator->locate($path, $namespace, $this->app->make(Finder::class))];
        }

        return $classes;
    }
}
