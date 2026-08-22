<?php

use Electrik\Http\Middleware\EnsureOnboardingComplete;
use Electrik\Http\Middleware\EnsureTeamSelected;
use Electrik\Http\Middleware\SetPermissionsTeamId;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class, EnsureTeamSelected::class, EnsureOnboardingComplete::class])
    ->prefix('billing')
    ->name('billing.')
    ->group(function () {
        Route::livewire('/', 'electrik.billing.index')->name('index');
        Route::livewire('/plans', 'electrik.billing.plans')->name('plans');
        Route::livewire('/subscription', 'electrik.billing.subscription')->name('subscription');
        Route::livewire('/payment-methods', 'electrik.billing.payment-methods')->name('payment-methods');
        Route::livewire('/address', 'electrik.billing.address')->name('address');
        Route::livewire('/invoices', 'electrik.billing.invoices')->name('invoices');
    });
