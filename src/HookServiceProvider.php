<?php

namespace MorningMedley\Hook;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Hook\Console\HookCacheCommand;
use MorningMedley\Hook\Console\HookClearCommand;
use MorningMedley\Hook\Console\HookMakeCommand;
use \MorningMedley\Facades\Hook as HookFacade;

class HookServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'hook');

        HookFacade::setFacadeApplication($this->app);
        $this->app->singleton(Hook::class);

        $this->app->make(Hook::class)->locate();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->optimizes(
                optimize: 'hook:cache',
                clear: 'hook:clear',
            );
        }

        $this->app->make(Hook::class)
            ->boot();

        $this->commands([
            HookMakeCommand::class,
            HookCacheCommand::class,
            HookClearCommand::class,
        ]);
    }
}
