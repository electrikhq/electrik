<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function($route) {

	$route->middleware(['guest'])->group(function($route) {

		$route->get('login', Electrik\Http\Livewire\Auth\Login::class)->name('login');
		$route->get('forgot-password', Electrik\Http\Livewire\Auth\ForgotPassword::class)->name('forgot-password');
		$route->get('reset-password/{token?}', Electrik\Http\Livewire\Auth\ResetPassword::class)->name('password.reset');

	});

	$route->get('dashboard', Electrik\Http\Livewire\Dashboard\Index::class)->name('dashboard.index');

	$route->name('onboarding.')->prefix('onboarding')->group(function($route) {

		$route->get('choose-plan', Electrik\Http\Livewire\Onboarding\ChoosePlan::class)->name('choose.plan');
		$route->get('register', Electrik\Http\Livewire\Onboarding\Register::class)->name('register');
		$route->get('confirm', Electrik\Http\Livewire\Onboarding\Confirm::class)->name('confirm');

	});



	$route->get('teams/accept/{token}', Electrik\Http\Livewire\Auth\Teams\AcceptInvite::class)->name('teams.invite.accept');
	$route->get('teams/register', Electrik\Http\Livewire\Auth\Teams\Register::class)->name('teams.invite.register');
	$route->get('teams/login', Electrik\Http\Livewire\Auth\Teams\Login::class)->name('teams.invite.login');



	$route->middleware(['auth'])->group(function($route) {

		$route->get('/', function () {
			return redirect()->route('dashboard.index');
		})->name('home');
		
		$route->get('dashboard', Electrik\Http\Livewire\Dashboard\Index::class)->name('dashboard.index');

		$route->name('settings.')->prefix('settings')->group(function($route){
			$route->get('personal', Electrik\Http\Livewire\Settings\Personal::class)->name('personal');
			$route->get('email', Electrik\Http\Livewire\Settings\Email::class)->name('email');
		});

		$route->name('billing.')->prefix('billing')->group(function($route) {
			$route->get('/', Electrik\Http\Livewire\Billing\Index::class)->name('index');
			$route->get('subscription', Electrik\Http\Livewire\Billing\Subscription::class)->name('subscription');
			$route->get('invoices', Electrik\Http\Livewire\Billing\Invoices::class)->name('invoices');
		});

		// $route->name('subscriptions.')->prefix('subscriptions')->group(function($route) {
		// 	$route->get('/', Electrik\Http\Livewire\Subscriptions\Index::class)->name('index');
		// });

		$route->name('teams.')->prefix('teams')->group(function($route) {
			$route->get('create', Electrik\Http\Livewire\Teams\Create::class)->name('create');
			$route->get('settings', Electrik\Http\Livewire\Teams\Settings::class)->name('settings');

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
				$route->get('/', Electrik\Http\Livewire\Teams\Members\Index::class)->name('index');
				$route->get('invited', Electrik\Http\Livewire\Teams\Members\Invited::class)->name('invited');
				$route->get('edit/{user}', Electrik\Http\Livewire\Teams\Members\Edit::class)->name('edit');
			});

			$route->name('roles.')->prefix('roles')->group(function($route) {
				$route->get('/', \Electrik\Http\Livewire\Roles\Index::class)->name('index');
				$route->get('create', \Electrik\Http\Livewire\Roles\Create::class)->name('create');
				$route->get('edit/{role}', \Electrik\Http\Livewire\Roles\Edit::class)->name('edit');

			});
				
			$route->name('permissions.')->prefix('permissions')->group(function($route) {
				$route->get('/', \Electrik\Http\Livewire\Permissions\Index::class)->name('index');
				$route->get('edit/{permission}', \Electrik\Http\Livewire\Permissions\Edit::class)->name('edit');

			});

		});


	});

	$route->get('logout', function() {

	})->name('logout');


});