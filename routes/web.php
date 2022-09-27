<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function($route) {

	$route->get('hello', \Electrik\Http\Livewire\HelloWorld::class)->name('hello-world');

});