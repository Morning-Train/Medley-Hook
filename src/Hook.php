<?php

namespace MorningMedley\Hook;

use Illuminate\Contracts\Foundation\Application;
use MorningMedley\Hook\Classes\HookLocator;
use Symfony\Component\Finder\Finder;

class Hook
{
    protected array $hooks = [];

    public function __construct(protected Application $app)
    {
    }

    public function locate()
    {
        if ($this->hooksAreCached()) {
            $this->hooks = require $this->getCachePath();
        } else {
            $paths = $this->app->make('config')->get('hook.paths');
            foreach ($paths as $hookPath) {
                [$namespace, $path] = $hookPath;
                $locator = $this->app->make(HookLocator::class);
                $this->hooks = [
                    ...$this->hooks,
                    ...$locator->locate($path, $namespace, $this->app->make(Finder::class)),
                ];
            }
        }
    }

    public function hooks(): array
    {
        return $this->hooks;
    }

    public function boot()
    {
        foreach ($this->hooks as $class) {
            if (class_exists($class) && method_exists($class, 'hookClass')) {
                try {
                    $this->app->make($class)->hookClass();
                } catch (\Throwable $e) {

                }
            }
        }
    }

    public function getCachePath(): string
    {
        return $this->app->bootstrapPath('cache/hook.php');
    }

    protected function hooksAreCached(): bool
    {
        return file_exists($this->getCachePath());
    }

    public function cache()
    {

    }
}
