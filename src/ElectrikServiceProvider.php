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

        // Publish breadcrumbs config
        $this->publishes([
            __DIR__.'/../config/breadcrumbs.php' => config_path('breadcrumbs.php'),
        ], 'electrik-breadcrumbs');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/electrik'),
        ], 'electrik-views');

        // Register middleware aliases
        $this->app['router']->aliasMiddleware('electrik.team', \Electrik\Middleware\EnsureTeamSelected::class);
        $this->app['router']->aliasMiddleware('electrik.subscription', \Electrik\Middleware\EnsureSubscriptionActive::class);

        // Load routes (only if at least one component exists - they're copied during installation)
        // Check if Dashboard component exists as a signal that installation has been run
        if (file_exists(__DIR__.'/../routes/web.php') && class_exists('App\Livewire\Dashboard\Index')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
        
        // Breadcrumbs are loaded automatically by diglactic/laravel-breadcrumbs
        // based on config('breadcrumbs.files') which points to routes/breadcrumbs.php
        // No need to manually load them here
    }

    /**
     * Register event listeners.
     *
     * @return void
     */
    protected function registerEventListeners()
    {
        // Only register listeners if the classes exist (they're copied during installation)
        // User events
        if (class_exists(\App\Events\User\UserRegistered::class)) {
            if (class_exists(\App\Listeners\User\SendWelcomeEmail::class)) {
                Event::listen(
                    \App\Events\User\UserRegistered::class,
                    \App\Listeners\User\SendWelcomeEmail::class
                );
            }

            if (class_exists(\App\Listeners\User\CreateDefaultTeam::class)) {
                Event::listen(
                    \App\Events\User\UserRegistered::class,
                    \App\Listeners\User\CreateDefaultTeam::class
                );
            }
        }

        // Team events
        if (class_exists(\App\Events\Team\TeamCreated::class) && class_exists(\App\Listeners\Team\CreateStripeCustomer::class)) {
            Event::listen(
                \App\Events\Team\TeamCreated::class,
                \App\Listeners\Team\CreateStripeCustomer::class
            );
        }

        if (class_exists(\App\Events\Team\MemberInvited::class) && class_exists(\App\Listeners\Team\SendInvitationEmail::class)) {
            Event::listen(
                \App\Events\Team\MemberInvited::class,
                \App\Listeners\Team\SendInvitationEmail::class
            );
        }

        // Billing events
        if (class_exists(\App\Events\Billing\SubscriptionCreated::class)) {
            if (class_exists(\App\Listeners\Billing\SyncStripeCustomer::class)) {
                Event::listen(
                    \App\Events\Billing\SubscriptionCreated::class,
                    \App\Listeners\Billing\SyncStripeCustomer::class
                );
            }

            if (class_exists(\App\Listeners\Billing\SendConfirmationEmail::class)) {
                Event::listen(
                    \App\Events\Billing\SubscriptionCreated::class,
                    \App\Listeners\Billing\SendConfirmationEmail::class
                );
            }
        }

        if (class_exists(\App\Events\Billing\PaymentFailed::class) && class_exists(\App\Listeners\Billing\HandlePaymentFailure::class)) {
            Event::listen(
                \App\Events\Billing\PaymentFailed::class,
                \App\Listeners\Billing\HandlePaymentFailure::class
            );
        }

        // Permission events
        if (class_exists(\App\Events\Permission\RoleAssigned::class) && class_exists(\App\Listeners\Permission\LogRoleAssigned::class)) {
            Event::listen(
                \App\Events\Permission\RoleAssigned::class,
                \App\Listeners\Permission\LogRoleAssigned::class
            );
        }

        if (class_exists(\App\Events\Permission\PermissionGranted::class) && class_exists(\App\Listeners\Permission\LogPermissionGranted::class)) {
            Event::listen(
                \App\Events\Permission\PermissionGranted::class,
                \App\Listeners\Permission\LogPermissionGranted::class
            );
        }
    }
}

