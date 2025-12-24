<?php

namespace Electrik;

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
}

