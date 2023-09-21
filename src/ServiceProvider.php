<?php

namespace Morningtrain\WP\Hooks;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Symfony\Component\Finder\Finder;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'wp-hooks');
    }

    public function boot(): void
    {
        $paths = $this->app->get('config')->get('wp-hooks.path');
        foreach ($paths as $namespace => $path) {
            $finder = new Finder();
            $finder->in($this->app->basePath($path))->name('*.php')->files();
            foreach ($finder as $file) {
                $class_name = rtrim($namespace, '\\') . '\\' . $file->getFilenameWithoutExtension();
                if (class_exists($class_name)) {
                    try {
                        new $class_name();
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }
        }

    }
}
