<?php

namespace MorningMedley\Hook;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use MorningMedley\Hook\Classes\HookCollection;
use MorningMedley\Hook\Classes\HookLocator;
use Symfony\Component\Finder\Finder;

class Hook
{
    protected array $paths = [];
    protected HookCollection $hookCollection;

    public function __construct(protected Application $app)
    {
        $this->hookCollection = $this->app->make(HookCollection::class);
    }

    public function boot(): void
    {
        $this->hookCollection->load();
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
            $this->hookCollection->add(require $this->getCachePath());
        } else {
            $paths = $this->app->make('config')->get('hook.paths');
            foreach ([...$paths, ...$this->paths] as $hookPath) {
                [$namespace, $path] = $hookPath;
                $locator = $this->app->make(HookLocator::class);
                $this->hookCollection->add($locator->locate($path, $namespace, $this->app->make(Finder::class)));
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
        return $this->hookCollection->hooks();
    }

    /**
     * Access the current HookCollection
     *
     * @return HookCollection
     */
    public function hookCollection(): HookCollection
    {
        return $this->hookCollection;
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
