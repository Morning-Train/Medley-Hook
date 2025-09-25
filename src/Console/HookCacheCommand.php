<?php

namespace MorningMedley\Hook\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Filesystem\Filesystem;
use MorningMedley\Facades\Hook;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'hook:cache')]
class HookCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'hook:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster hook loading';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config cache command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function handle()
    {
        // Clear existing hook file
        $this->callSilent('hook:clear');

        $hooks = $this->getFreshList();

        $configPath = Hook::getCachePath();

        $success = $this->files->put(
            $configPath, '<?php return ' . var_export($hooks, true) . ';' . PHP_EOL
        );

        if ($success === false) {
            $this->components->error('Failed to write cache file.');
        }

        $this->components->info('Hooks cached successfully.');
    }

    /**
     * Boot a fresh copy of the hook list
     *
     * @return array
     */
    protected function getFreshList()
    {
        ray("getFreshList");
        $app = require $this->laravel->bootstrapPath('app.php');

        $app->useStoragePath($this->laravel->storagePath());
        $app->make(ConsoleKernelContract::class)->bootstrap();
        ray(Hook::hooks());

        return Hook::hooks();
    }
}
