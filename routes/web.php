<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function($route) {

	$route->get('hello', \Electrik\Http\Livewire\HelloWorld::class)->name('hello-world');
	$route->get('dashboard', \Electrik\Http\Livewire\Dashboard\Index::class)->name('dashboard.index');



	$route->name('onboarding.')->prefix('onboarding')->group(function($route) {

		$route->get('choose-plan', Electrik\Http\Livewire\Onboarding\ChoosePlan::class)->name('choose.plan');
		$route->get('register', Electrik\Http\Livewire\Onboarding\Register::class)->name('register');
		$route->get('confirm', Electrik\Http\Livewire\Onboarding\Confirm::class)->name('confirm');

	});


});