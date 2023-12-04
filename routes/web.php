<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function($route) {

	$route->middleware(['guest'])->group(function($route) {

		$route->get('login', App\Livewire\Auth\Login::class)->name('login');
		$route->get('forgot-password', App\Livewire\Auth\ForgotPassword::class)->name('forgot-password');
		$route->get('reset-password/{token?}', App\Livewire\Auth\ResetPassword::class)->name('password.reset');

	});

	$route->get('dashboard', App\Livewire\Dashboard\Index::class)->name('dashboard.index');

	$route->name('onboarding.')->prefix('onboarding')->group(function($route) {

		$route->get('choose-plan', App\Livewire\Onboarding\ChoosePlan::class)->name('choose.plan');
		$route->get('register', App\Livewire\Onboarding\Register::class)->name('register');
		$route->get('confirm', App\Livewire\Onboarding\Confirm::class)->name('confirm');

	});



	$route->get('teams/accept/{token}', App\Livewire\Auth\Teams\AcceptInvite::class)->name('teams.invite.accept');
	$route->get('teams/register', App\Livewire\Auth\Teams\Register::class)->name('teams.invite.register');
	$route->get('teams/login', App\Livewire\Auth\Teams\Login::class)->name('teams.invite.login');



	$route->middleware(['auth'])->group(function($route) {

		$route->get('/', function () {
			return redirect()->route('dashboard.index');
		})->name('home');
		
		$route->get('dashboard', App\Livewire\Dashboard\Index::class)->name('dashboard.index');

		$route->name('settings.')->prefix('settings')->group(function($route){
			$route->get('personal', App\Livewire\Settings\Personal::class)->name('personal');
			$route->get('email', App\Livewire\Settings\Email::class)->name('email');
		});

		$route->name('billing.')->prefix('billing')->group(function($route) {
			$route->get('/', App\Livewire\Billing\Index::class)->name('index');
			$route->get('subscription', App\Livewire\Billing\Subscription::class)->name('subscription');
			$route->get('invoices', App\Livewire\Billing\Invoices::class)->name('invoices');
		});

		// $route->name('subscriptions.')->prefix('subscriptions')->group(function($route) {
		// 	$route->get('/', App\Livewire\Subscriptions\Index::class)->name('index');
		// });

		$route->name('teams.')->prefix('teams')->group(function($route) {
			$route->get('create', App\Livewire\Teams\Create::class)->name('create');
			$route->get('settings', App\Livewire\Teams\Settings::class)->name('settings');

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
				$route->get('/', App\Livewire\Teams\Members\Index::class)->name('index');
				$route->get('invited', App\Livewire\Teams\Members\Invited::class)->name('invited');
				$route->get('edit/{user}', App\Livewire\Teams\Members\Edit::class)->name('edit');
			});

			$route->name('roles.')->prefix('roles')->group(function($route) {
				$route->get('/', \App\Livewire\Roles\Index::class)->name('index');
				$route->get('create', \App\Livewire\Roles\Create::class)->name('create');
				$route->get('edit/{role}', \App\Livewire\Roles\Edit::class)->name('edit');

			});
				
			$route->name('permissions.')->prefix('permissions')->group(function($route) {
				$route->get('/', \App\Livewire\Permissions\Index::class)->name('index');
				$route->get('edit/{permission}', \App\Livewire\Permissions\Edit::class)->name('edit');

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