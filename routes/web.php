<?php

use Illuminate\Support\Facades\Route;
use Electrik\Middleware\EnsureTeamSelected;
use Electrik\Middleware\EnsureSubscriptionActive;

/*
|--------------------------------------------------------------------------
| Electrik Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the ElectrikServiceProvider. They handle
| authentication, teams, billing, and dashboard functionality.
|
| Note: Routes are only registered if the corresponding Livewire components exist.
| Components are copied to app/ during installation.
|
*/

// Guest routes (no authentication required)
Route::middleware('guest')->group(function () {
    if (class_exists('App\Livewire\Auth\Login')) {
        Route::get('/login', 'App\Livewire\Auth\Login')->name('login');
    }
    if (class_exists('App\Livewire\Auth\Register')) {
        Route::get('/register', 'App\Livewire\Auth\Register')->name('register');
    }
    if (class_exists('App\Livewire\Auth\ForgotPassword')) {
        Route::get('/forgot-password', 'App\Livewire\Auth\ForgotPassword')->name('password.request');
    }
    if (class_exists('App\Livewire\Auth\ResetPassword')) {
        Route::get('/reset-password/{token}', 'App\Livewire\Auth\ResetPassword')->name('password.reset');
    }
});

// Authenticated routes (require authentication)
Route::middleware('auth')->group(function () {
    // Dashboard
    if (class_exists('App\Livewire\Dashboard\Index')) {
        Route::get('/dashboard', 'App\Livewire\Dashboard\Index')->name('dashboard.index');
    }
    
    // Team routes (require team selection)
    Route::middleware([EnsureTeamSelected::class])->prefix('teams')->name('teams.')->group(function () {
        if (class_exists('App\Livewire\Teams\Index')) {
            Route::get('/', 'App\Livewire\Teams\Index')->name('index');
        }
        if (class_exists('App\Livewire\Teams\Create')) {
            Route::get('/create', 'App\Livewire\Teams\Create')->name('create');
        }
        if (class_exists('App\Livewire\Teams\Settings')) {
            Route::get('/{team}/settings', 'App\Livewire\Teams\Settings')->name('settings');
        }
        if (class_exists('App\Livewire\Teams\Members\Index')) {
            Route::get('/{team}/members', 'App\Livewire\Teams\Members\Index')->name('members.index');
        }
        if (class_exists('App\Livewire\Teams\Members\Invite')) {
            Route::get('/{team}/members/invite', 'App\Livewire\Teams\Members\Invite')->name('members.invite');
        }
        Route::post('/switch/{team}', function ($team) {
            if (class_exists('App\Actions\Teams\SwitchTeam') && class_exists('App\Models\Team')) {
                $teamModel = \App\Models\Team::findOrFail($team);
                $action = new \App\Actions\Teams\SwitchTeam();
                $action->execute(auth()->user(), $teamModel);
            }
            return redirect()->back();
        })->name('switch');
    });
    
    // Team invitation acceptance (no team required)
    if (class_exists('App\Livewire\Teams\Invite\Accept')) {
        Route::get('/teams/invite/accept/{token}', 'App\Livewire\Teams\Invite\Accept')->name('teams.invite.accept');
    }
    
    // Billing routes (require team selection)
    Route::middleware([EnsureTeamSelected::class])->prefix('billing')->name('billing.')->group(function () {
        if (class_exists('App\Livewire\Billing\Index')) {
            Route::get('/', 'App\Livewire\Billing\Index')->name('index');
        }
        if (class_exists('App\Livewire\Billing\Plans')) {
            Route::get('/plans', 'App\Livewire\Billing\Plans')->name('plans');
        }
        if (class_exists('App\Livewire\Billing\Subscription')) {
            Route::get('/subscription', 'App\Livewire\Billing\Subscription')->name('subscription');
        }
        if (class_exists('App\Livewire\Billing\PaymentMethods')) {
            Route::get('/payment-methods', 'App\Livewire\Billing\PaymentMethods')->name('payment-methods');
        }
        if (class_exists('App\Livewire\Billing\Address')) {
            Route::get('/address', 'App\Livewire\Billing\Address')->name('address');
        }
        if (class_exists('App\Livewire\Billing\Invoices')) {
            Route::get('/invoices', 'App\Livewire\Billing\Invoices')->name('invoices');
        }
    });
    
    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        if (class_exists('App\Livewire\Settings\Profile')) {
            Route::get('/profile', 'App\Livewire\Settings\Profile')->name('profile');
        }
        if (class_exists('App\Livewire\Settings\Security')) {
            Route::get('/security', 'App\Livewire\Settings\Security')->name('security');
        }
    });
});

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');
