<?php

namespace Electrik\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'electrik:make {name} {--without-model} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Electrik Livewire component';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Electrik component';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/component.php.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Livewire';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->getNameInput();
        $name = str_replace('\\', '/', $name);
        $modelName = $this->option('model') ?: Str::studly(class_basename($name));

        // Create Livewire component
        parent::handle();

        // Create view
        $this->createView($name);

        $this->info("Livewire component {$name} created successfully.");

        // Create Model if not excluded
        if (!$this->option('without-model')) {
            $this->call('make:model', ['name' => $modelName]);
            $this->info("Model {$modelName} created successfully.");
        }

        return 0;
    }

    /**
     * Create a view file for the component.
     *
     * @param  string  $name
     * @return void
     */
    protected function createView($name)
    {
        $viewPath = resource_path('views/livewire/'.str_replace('\\', '/', $name).'.blade.php');
        $stub = file_get_contents(__DIR__.'/../../stubs/view.blade.php.stub');

        if (!file_exists($dir = dirname($viewPath))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($viewPath, $stub);
    }
}

