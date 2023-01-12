<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function($route) {

	$route->middleware(['guest'])->group(function($route) {

		$route->get('login', App\Http\Livewire\Auth\Login::class)->name('login');
		$route->get('forgot-password', App\Http\Livewire\Auth\ForgotPassword::class)->name('forgot-password');
		$route->get('reset-password/{token?}', App\Http\Livewire\Auth\ResetPassword::class)->name('password.reset');

	});

	$route->get('dashboard', App\Http\Livewire\Dashboard\Index::class)->name('dashboard.index');

	$route->name('onboarding.')->prefix('onboarding')->group(function($route) {

		$route->get('choose-plan', App\Http\Livewire\Onboarding\ChoosePlan::class)->name('choose.plan');
		$route->get('register', App\Http\Livewire\Onboarding\Register::class)->name('register');
		$route->get('confirm', App\Http\Livewire\Onboarding\Confirm::class)->name('confirm');

	});



	$route->get('teams/accept/{token}', App\Http\Livewire\Auth\Teams\AcceptInvite::class)->name('teams.invite.accept');
	$route->get('teams/register', App\Http\Livewire\Auth\Teams\Register::class)->name('teams.invite.register');
	$route->get('teams/login', App\Http\Livewire\Auth\Teams\Login::class)->name('teams.invite.login');



	$route->middleware(['auth'])->group(function($route) {

		$route->get('/', function () {
			return redirect()->route('dashboard.index');
		})->name('home');
		
		$route->get('dashboard', App\Http\Livewire\Dashboard\Index::class)->name('dashboard.index');

		$route->name('settings.')->prefix('settings')->group(function($route){
			$route->get('personal', App\Http\Livewire\Settings\Personal::class)->name('personal');
			$route->get('email', App\Http\Livewire\Settings\Email::class)->name('email');
		});

		$route->name('billing.')->prefix('billing')->group(function($route) {
			$route->get('/', App\Http\Livewire\Billing\Index::class)->name('index');
			$route->get('subscription', App\Http\Livewire\Billing\Subscription::class)->name('subscription');
			$route->get('invoices', App\Http\Livewire\Billing\Invoices::class)->name('invoices');
		});

		// $route->name('subscriptions.')->prefix('subscriptions')->group(function($route) {
		// 	$route->get('/', App\Http\Livewire\Subscriptions\Index::class)->name('index');
		// });

		$route->name('teams.')->prefix('teams')->group(function($route) {
			$route->get('create', App\Http\Livewire\Teams\Create::class)->name('create');
			$route->get('settings', App\Http\Livewire\Teams\Settings::class)->name('settings');

			Route::get('switch/{id}', function($id) {

				$teamModel = config('teamwork.team_model');
		
				$team = $teamModel::findOrFail($id);
		
				try {
					auth()->user()->switchTeam($team);
				} catch (\Exception $e) {
					abort(403);
				}
		
				return redirect()->back();
		
			})->name('switch');
			
			$route->name('members.')->prefix('members')->group(function($route) {
				$route->get('/', App\Http\Livewire\Teams\Members\Index::class)->name('index');
				$route->get('invited', App\Http\Livewire\Teams\Members\Invited::class)->name('invited');
				$route->get('edit/{user}', App\Http\Livewire\Teams\Members\Edit::class)->name('edit');
			});

			$route->name('roles.')->prefix('roles')->group(function($route) {
				$route->get('/', \App\Http\Livewire\Roles\Index::class)->name('index');
				$route->get('create', \App\Http\Livewire\Roles\Create::class)->name('create');
				$route->get('edit/{role}', \App\Http\Livewire\Roles\Edit::class)->name('edit');

			});
				
			$route->name('permissions.')->prefix('permissions')->group(function($route) {
				$route->get('/', \App\Http\Livewire\Permissions\Index::class)->name('index');
				$route->get('edit/{permission}', \App\Http\Livewire\Permissions\Edit::class)->name('edit');

			});

		});


	});

	$route->get('logout', function(Request $request) {
		
		Auth::guard('web')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect('/');

	})->name('logout');

});