<?php

namespace MorningMedley\Hook;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Hook\Classes\HookLocator;
use MorningMedley\Hook\Console\HookMakeCommand;
use Symfony\Component\Finder\Finder;

class HookServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'hook');
    }

    public function boot(): void
    {
        if (! $this->app->configurationIsCached()) {
            $classes = $this->findClasses();
            $this->app['config']->set('hook.classes', $classes);
        }

        foreach ($this->app['config']->get('hook.classes') as $class) {
            if (class_exists($class) && method_exists($class, 'hookClass')) {
                try {
                    $this->app->make($class)->hookClass();
                } catch (\Throwable $e) {

                }
            }
        }

        $this->commands([
            HookMakeCommand::class
        ]);
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
