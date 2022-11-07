<?php

namespace Electrik;

use App\Http\Livewire\HelloWorld;
use App\Models\Team;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use ReflectionClass;
use Illuminate\Support\Str;
use SplFileInfo;
use Cashier;


class ElectrikServiceProvider extends ServiceProvider {
    
	/**
	 * Bootstrap any package services.
	 *
	 * @return void
	 */
	public function boot() {

		if ($this->app->runningInConsole()) {
			$this->commands([
				\Electrik\Console\InstallCommand::class,
				\Electrik\Console\MakeCommand::class,
			]);
		}
	}

	public function register() {

		// $this->mergeConfigFrom(__DIR__.'/../config/plans.php', 'plans');
		
	}
	

	// protected function registerLivewireComponentDirectory($directory, $namespace, string $aliasPrefix = '') {

    //     collect((new Filesystem)->allFiles($directory))
    //         ->map(function (SplFileInfo $file) use ($namespace) {
    //             return (string) Str::of($namespace)
    //                 ->append('\\', $file->getRelativePathname())
    //                 ->replace(['/', '.php'], ['\\', '']);
    //         })
    //         ->each(function ($class) use ($namespace, $aliasPrefix) {
    //             $alias = Str::of($class)
    //                 ->after($namespace . '\\')
    //                 ->replace(['/', '\\'], '.')
    //                 ->prepend($aliasPrefix)
    //                 ->explode('.')
    //                 ->map([Str::class, 'kebab'])
    //                 ->implode('.');

    //             Livewire::component($alias, $class);
    //         });
	// }


}