<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {

    // Guest routes
    Route::middleware(['guest'])->group(function () {
        Route::get('login', \App\Livewire\Auth\Login::class)->name('login');
        Route::get('forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('forgot-password');
        Route::get('reset-password/{token?}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
    });

    // Dashboard
    Route::get('dashboard', \App\Livewire\Dashboard\Index::class)->name('dashboard.index');

    // Onboarding routes
    Route::name('onboarding.')->prefix('onboarding')->group(function () {
        Route::get('choose-plan', \App\Livewire\Onboarding\ChoosePlan::class)->name('choose.plan');
        Route::get('register', \App\Livewire\Onboarding\Register::class)->name('register');
        Route::get('confirm', \App\Livewire\Onboarding\Confirm::class)->name('confirm');
    });

    // Team invitation routes
    Route::get('teams/accept/{token}', \App\Livewire\Auth\Teams\AcceptInvite::class)->name('teams.invite.accept');
    Route::get('teams/register', \App\Livewire\Auth\Teams\Register::class)->name('teams.invite.register');
    Route::get('teams/login', \App\Livewire\Auth\Teams\Login::class)->name('teams.invite.login');

    // Authenticated routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('dashboard.index');
        })->name('home');

        // Settings
        Route::name('settings.')->prefix('settings')->group(function () {
            Route::get('personal', \App\Livewire\Settings\Personal::class)->name('personal');
            Route::get('email', \App\Livewire\Settings\Email::class)->name('email');
        });

        // Billing
        Route::name('billing.')->prefix('billing')->group(function () {
            Route::get('/', \App\Livewire\Billing\Index::class)->name('index');
            Route::get('subscription', \App\Livewire\Billing\Subscription::class)->name('subscription');
            Route::get('invoices', \App\Livewire\Billing\Invoices::class)->name('invoices');
        });

        // Teams
        Route::name('teams.')->prefix('teams')->group(function () {
            Route::get('create', \App\Livewire\Teams\Create::class)->name('create');
            Route::get('settings', \App\Livewire\Teams\Settings::class)->name('settings');

            Route::get('switch/{id}', function ($id) {
                // TODO: Implement team switching when team management is built
                // $teamModel = config('electrik.team_model', \App\Models\Team::class);
                // $team = $teamModel::findOrFail($id);
                // auth()->user()->switchTeam($team);
                // return redirect()->back();
                abort(501, 'Team switching not yet implemented');
            })->name('switch');

            // Team members
            Route::name('members.')->prefix('members')->group(function () {
                Route::get('/', \App\Livewire\Teams\Members\Index::class)->name('index');
                Route::get('invited', \App\Livewire\Teams\Members\Invited::class)->name('invited');
                Route::get('edit/{user}', \App\Livewire\Teams\Members\Edit::class)->name('edit');
            });

            // Roles
            Route::name('roles.')->prefix('roles')->group(function () {
                Route::get('/', \App\Livewire\Roles\Index::class)->name('index');
                Route::get('create', \App\Livewire\Roles\Create::class)->name('create');
                Route::get('edit/{role}', \App\Livewire\Roles\Edit::class)->name('edit');
            });

            // Permissions
            Route::name('permissions.')->prefix('permissions')->group(function () {
                Route::get('/', \App\Livewire\Permissions\Index::class)->name('index');
                Route::get('edit/{permission}', \App\Livewire\Permissions\Edit::class)->name('edit');
            });
        });
    });

    // Logout
    Route::get('logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

