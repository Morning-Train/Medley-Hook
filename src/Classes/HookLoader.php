<?php

namespace MorningMedley\Hook\Classes;

use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;

class HookLoader
{
    private Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function loadHooks(array $files, string $namespace)
    {
        foreach ($files as $name => $path) {
            $this->loadHookByFileAndNamespace($path, $name, $namespace);
        }
    }

    public function loadHookByFileAndNamespace(
        string $relativePath,
        string $filenameWithoutExtension,
        string $namespace
    ): void {
        $class_name = implode('\\', array_filter([
            rtrim($namespace, '\\'),
            str_replace("/", "\\", $relativePath),
            $filenameWithoutExtension,
        ]));
        if (class_exists($class_name)) {
            try {
                new $class_name();
            } catch (\Throwable $e) {

            }
        }
    }
}
