<?php

namespace Electrik;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ElectrikServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/electrik.php', 'electrik');
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Electrik\Console\InstallCommand::class,
                \Electrik\Console\MakeCommand::class,
                \Electrik\Console\SyncStripeCommand::class,
            ]);
        }

        // Register event listeners
        $this->registerEventListeners();

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'electrik-migrations');

        // Publish config files
        $this->publishes([
            __DIR__.'/../config/electrik.php' => config_path('electrik.php'),
        ], 'electrik-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/electrik'),
        ], 'electrik-views');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Register event listeners.
     *
     * @return void
     */
    protected function registerEventListeners()
    {
        // User events
        Event::listen(
            \App\Events\User\UserRegistered::class,
            \App\Listeners\User\SendWelcomeEmail::class
        );

        Event::listen(
            \App\Events\User\UserRegistered::class,
            \App\Listeners\User\CreateDefaultTeam::class
        );

        // Team events
        Event::listen(
            \App\Events\Team\TeamCreated::class,
            \App\Listeners\Team\CreateStripeCustomer::class
        );

        Event::listen(
            \App\Events\Team\MemberInvited::class,
            \App\Listeners\Team\SendInvitationEmail::class
        );

        // Billing events
        Event::listen(
            \App\Events\Billing\SubscriptionCreated::class,
            \App\Listeners\Billing\SyncStripeCustomer::class
        );

        Event::listen(
            \App\Events\Billing\SubscriptionCreated::class,
            \App\Listeners\Billing\SendConfirmationEmail::class
        );

        Event::listen(
            \App\Events\Billing\PaymentFailed::class,
            \App\Listeners\Billing\HandlePaymentFailure::class
        );

        // Permission events
        Event::listen(
            \App\Events\Permission\RoleAssigned::class,
            \App\Listeners\Permission\LogRoleAssigned::class
        );

        Event::listen(
            \App\Events\Permission\PermissionGranted::class,
            \App\Listeners\Permission\LogPermissionGranted::class
        );
    }
}

