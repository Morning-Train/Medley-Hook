<?php

namespace MorningMedley\Hook\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:hook')]
class HookMakeCommand extends GeneratorCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new hook';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:hook';

    /**
     * The type of file being generated.
     *
     * @var string
     */
    protected $type = 'Hook';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        [$className, $namespace] = $this->parseName($this->getNameInput());

        $contents = parent::buildClass($name);

        return str_replace(
            ['{{ classname }}', '{{ hooknamespace }}'],
            [$className, $namespace],
            $contents,
        );
    }

    /**
     * Get the destination view path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->hookPath(
            $this->getNameInput() . '.php',
        );
    }

    protected function hookPath($path = '')
    {
        $basePath = $this->laravel['config']['hook.paths'][0][1] ?? 'app/Hooks';

        $path = implode(DIRECTORY_SEPARATOR, [$basePath, $path]);

        return $this->getLaravel()->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    protected function parseName($name)
    {
        $parts = explode('/', $name);
        $className = array_pop($parts);

        $baseNamespace = $this->laravel['config']['hook.paths'][0][0] ?? 'Hooks';
        $nameSpace = implode('\\', [$baseNamespace, ...$parts]);

        return [$className, $nameSpace];
    }

    /**
     * Get the desired hook name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));

        return $name;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath(
            '/stubs/hook.stub',
        );
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the view even if the view already exists'],
        ];
    }
}
