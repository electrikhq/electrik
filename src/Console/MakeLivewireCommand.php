<?php

namespace Electrik\Console;

use Illuminate\Support\Str;

class MakeLivewireCommand extends MakeCommand
{
    protected $signature = 'electrik:make:livewire
                            {name : Livewire component class name (e.g. Posts/Index)}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate a Livewire component with a Slate-styled Blade view';

    public function handle(): int
    {
        $raw = (string) $this->argument('name');
        $root = $this->laravel->getNamespace().'Livewire';
        $class = $this->qualifyClass($raw, $root);
        $basename = $this->classBasename($class);
        $relativeNs = $this->relativeNamespace($class, $root);

        $classPath = $this->pathFromClass($class);
        $viewRelative = 'livewire/'.Str::of(($relativeNs ? $relativeNs.'\\' : '').$basename)
            ->replace('\\', '/')
            ->explode('/')
            ->map(fn (string $part) => Str::kebab($part))
            ->implode('/');
        $viewPath = resource_path('views/'.$viewRelative.'.blade.php');
        $viewName = str_replace('/', '.', $viewRelative);

        $namespace = $relativeNs === ''
            ? rtrim($root, '\\')
            : $root.'\\'.$relativeNs;

        $ok = $this->writeStub('livewire.stub', $classPath, [
            '{{ namespace }}' => $namespace,
            '{{ class }}' => $basename,
            '{{ view }}' => $viewName,
        ]);

        if (! $ok) {
            return self::FAILURE;
        }

        $title = Str::headline($basename);
        $this->writeStub('livewire-view.stub', $viewPath, [
            '{{ title }}' => $title,
        ]);

        return self::SUCCESS;
    }
}
