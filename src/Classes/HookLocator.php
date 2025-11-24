<?php

namespace MorningMedley\Hook\Classes;

use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class HookLocator
{
    private Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param  string  $path
     * @param  string  $namespace
     * @param  Finder  $finder
     *
     * @return string[]
     */
    public function locate(string $path, string $namespace, Finder $finder): array
    {
        if (! is_dir($path)) {
            $path = $this->app->basePath($path);
        }

        $files = $this->files($path, $finder);
        $classes = $this->guessClassNames($files, $namespace);

        return $classes;
    }

    /**
     * @param  string  $path
     * @param  Finder  $finder
     *
     * @return SplFileInfo[]
     */
    public function files(string $path, Finder $finder): array
    {
        $files = [];
        $finder->in($path)->name('*.php')->notName('index.php')->files();

        foreach ($finder as $file) {
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @param  SplFileInfo[]  $files
     * @param  string  $namespace
     * @return string[]
     */
    public function guessClassNames(array $files, string $namespace): array
    {
        $classes = [];
        foreach ($files as $file) {
            $classes[] = $this->guessClassName($file, $namespace);
        }

        return $classes;
    }

    /**
     * @param  SplFileInfo  $file
     * @param  string  $namespace
     *
     * @return string
     */
    public function guessClassName(
        SplFileInfo $file,
        string $namespace
    ): string {
        return implode('\\', array_filter([
            rtrim($namespace, '\\'),
            str_replace("/", "\\", $file->getRelativePath()),
            $file->getFilenameWithoutExtension(),
        ]));
    }
}
