<?php

namespace MorningMedley\Hook;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use MorningMedley\Hook\Classes\HookLocator;
use Symfony\Component\Finder\Finder;

class Hook
{
    protected array $hooks = [];
    protected array $paths = [];

    public function __construct(protected Application $app)
    {

    }

    /**
     * Resolves path or cache into array of fully qualified classnames for hooks to load
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function locate(): void
    {
        if ($this->hooksAreCached()) {
            $this->hooks = require $this->getCachePath();
        } else {
            $paths = $this->app->make('config')->get('hook.paths');
            foreach ([...$paths, ...$this->paths] as $hookPath) {
                [$namespace, $path] = $hookPath;
                $locator = $this->app->make(HookLocator::class);
                $this->hooks = [
                    ...$this->hooks,
                    ...$locator->locate($path, $namespace, $this->app->make(Finder::class)),
                ];
            }
        }
    }

    /**
     * Register a path containing hooks for loading
     * If called before application boot() then this will be cached
     *
     * @param  string  $namespace
     * @param  string  $path
     *
     * @return void
     */
    public function register(string $namespace, string $path): void
    {
        $this->paths[] = [$namespace, $path];
    }

    /**
     * All registered Hook Classes
     *
     * @return string[]
     */
    public function hooks(): array
    {
        return $this->hooks;
    }

    /**
     * Load all registered and found hook classes
     * This method constructs all the classes and tries to call `hookClass()` on them
     *
     * @return void
     */
    public function loadHooks(): void
    {
        foreach ($this->hooks() as $class) {
            if (class_exists($class) && method_exists($class, 'hookClass')) {
                try {
                    $this->app->make($class)->hookClass();
                } catch (\Throwable $e) {

                }
            }
        }
    }

    /**
     * Get absolute path to cache file
     *
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->app->bootstrapPath('cache/hook.php');
    }

    /**
     * Checks if hook cache file exists
     *
     * @return bool
     */
    protected function hooksAreCached(): bool
    {
        return file_exists($this->getCachePath());
    }
}
