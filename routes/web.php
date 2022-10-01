<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function($route) {


	$route->middleware(['guest'])->group(function($route) {

		$route->get('login', Electrik\Http\Livewire\Auth\Login::class)->name('login');
		$route->get('forgot-password', Electrik\Http\Livewire\Auth\ForgotPassword::class)->name('forgot-password');
		$route->get('reset-password/{token?}', Electrik\Http\Livewire\Auth\ResetPassword::class)->name('password.reset');

	});


	$route->get('hello', Electrik\Http\Livewire\HelloWorld::class)->name('hello-world');
	$route->get('dashboard', Electrik\Http\Livewire\Dashboard\Index::class)->name('dashboard.index');



	$route->name('onboarding.')->prefix('onboarding')->group(function($route) {

		$route->get('choose-plan', Electrik\Http\Livewire\Onboarding\ChoosePlan::class)->name('choose.plan');
		$route->get('register', Electrik\Http\Livewire\Onboarding\Register::class)->name('register');
		$route->get('confirm', Electrik\Http\Livewire\Onboarding\Confirm::class)->name('confirm');

	});


});