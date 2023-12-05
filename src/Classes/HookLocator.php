<?php

namespace MorningMedley\Hook\Classes;

use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;

class HookLocator
{
    private Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function locate(string $path, string $namespace, Finder $finder): array
    {
        if (! is_dir($path)) {
            $path = $this->app->basePath($path);
        }

        $files = $this->files($path, $finder);
        $classes = $this->guessClassNames($files, $namespace);

        return $classes;
    }

    public function files(string $path, Finder $finder): array
    {
        $files = [];
        $finder->in($path)->name('*.php')->notName('index.php')->files();
        foreach ($finder as $file) {
            $files[$file->getFilenameWithoutExtension()] = $file->getRelativePath();
        }

        return $files;
    }

    public function guessClassNames(array $files, string $namespace)
    {
        $classes = [];
        foreach ($files as $name => $path) {
            $classes[] = $this->guessClassName($path, $name, $namespace);
        }

        return $classes;
    }

    public function guessClassName(
        string $relativePath,
        string $filenameWithoutExtension,
        string $namespace
    ): string {
        return implode('\\', array_filter([
            rtrim($namespace, '\\'),
            str_replace("/", "\\", $relativePath),
            $filenameWithoutExtension,
        ]));
    }
}
