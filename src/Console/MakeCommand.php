<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class MakeCommand extends Command
{
    protected function stubsPath(string $file): string
    {
        return dirname(__DIR__, 2).'/stubs/make/'.$file;
    }

    protected function qualifyClass(string $name, string $rootNamespace): string
    {
        $name = ltrim(str_replace('/', '\\', $name), '\\');

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $rootNamespace.'\\'.$name;
    }

    protected function classBasename(string $fqcn): string
    {
        return class_basename($fqcn);
    }

    protected function relativeNamespace(string $fqcn, string $rootNamespace): string
    {
        $relative = Str::after($fqcn, $rootNamespace.'\\');

        if (! str_contains($relative, '\\')) {
            return '';
        }

        return Str::beforeLast($relative, '\\');
    }

    protected function pathFromClass(string $fqcn): string
    {
        $relative = Str::after($fqcn, $this->laravel->getNamespace());
        $relative = str_replace('\\', '/', $relative);

        return app_path($relative.'.php');
    }

    protected function writeStub(string $stubFile, string $targetPath, array $replacements): bool
    {
        if (File::exists($targetPath) && ! $this->option('force')) {
            $this->components->error("File already exists: {$targetPath} (use --force)");

            return false;
        }

        $stub = File::get($this->stubsPath($stubFile));

        foreach ($replacements as $search => $replace) {
            $stub = str_replace($search, $replace, $stub);
        }

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $stub);
        $this->components->info("Created {$targetPath}");

        return true;
    }

    protected function studlyName(string $name): string
    {
        return Str::studly(str_replace(['/', '\\'], ' ', $name));
    }

    protected function tableName(string $modelBasename): string
    {
        return Str::snake(Str::pluralStudly($modelBasename));
    }
}
