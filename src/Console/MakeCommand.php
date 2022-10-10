<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class MakeCommand extends GeneratorCommand {

    protected $name = 'electrik:make';

    protected $description = 'Create a new Electrik Component';

	protected $stubsPath =  __DIR__ . '/../../stubs/';

    protected function getStub()
    {
        return $this->stubsPath . 'controllers/controller.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
		return $rootNamespace . '\Http\Livewire';
    }

    public function handle()
    {
        parent::handle();

        $this->makeController();
		$this->makeView();
    }
	
	protected function makeView() {
		
		if (!is_dir(dirname(resource_path().'/views/livewire/'.strtolower(str_replace("\\","/",$this->getNameInput()).'.blade.php')))) {
			// dir doesn't exist, make it
			mkdir(dirname(resource_path().'/views/livewire/'.strtolower(str_replace("\\","/",$this->getNameInput()).'.blade.php')), 0755, true);
		}

		  
        file_put_contents(
			resource_path().'/views/livewire/'.strtolower(str_replace("\\","/",$this->getNameInput()).'.blade.php'),
			file_get_contents($this->stubsPath.'/views/view.blade.php.stub')
        );
	}

    protected function makeController()
    {

		// Get the fully qualified class name (FQN)
        $class = $this->qualifyClass($this->getNameInput());

        // get the destination path, based on the default namespace
        $path = $this->getPath($class);

        $content = file_get_contents($path);

        // Update the file content with additional data (regular expressions)

        file_put_contents($path, $content);
    }
}