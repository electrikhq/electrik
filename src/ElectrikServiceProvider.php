<?php

namespace Electrik;

use Electrik\Http\Livewire\HelloWorld;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ElectrikServiceProvider extends ServiceProvider {
    
	public function boot() {
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'electrik');
		
		Livewire::component('electrik::hello-world', HelloWorld::class);

	}

	public function register() {
		$this->mergeConfigFrom(__DIR__.'/../config/livewire.php', 'livewire');
        // if (!$this->app->runningInConsole()) {
		// }

		// $this->registerLivewireComponents();


	}

	// $this->registerLivewireComponents();


}