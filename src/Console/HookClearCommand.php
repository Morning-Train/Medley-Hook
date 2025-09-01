<?php

namespace MorningMedley\Hook\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'hook:clear')]
class HookClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'hook:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the hook cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config clear command instance.
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
     */
    public function handle()
    {
        $this->files->delete(\MorningMedley\Facades\Hook::getCachePath());

        $this->components->info('Configuration cache cleared successfully.');
    }
}
