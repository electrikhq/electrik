<?php

namespace Electrik;

use Electrik\Http\Livewire\HelloWorld;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

class ElectrikServiceProvider extends ServiceProvider {
    
	/**
	 * Bootstrap any package services.
	 *
	 * @return void
	 */
	public function boot() {

		
		if (!$this->app->runningInConsole()) {
			
			if (class_exists(Livewire::class)) {
				
				$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
				$this->loadViewsFrom(__DIR__.'/../resources/views', 'electrik');
				// $this->app->afterResolving(BladeCompiler::class, function () {
					Livewire::component('electrik::hello-world', HelloWorld::class);
				// });
			};

		};

		if ($this->app->runningInConsole()) {

			$this->commands([
				\Electrik\Console\Install::class,
			]);

		}

	}

	public function register() {
		$this->mergeConfigFrom(__DIR__.'/../config/livewire.php', 'livewire');
	}

}