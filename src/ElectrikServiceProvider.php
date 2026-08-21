<?php

namespace Electrik;

use Electrik\Console\InstallCommand;
use Illuminate\Support\ServiceProvider;

class ElectrikServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/electrik.php', 'electrik');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'electrik');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/electrik.php' => config_path('electrik.php'),
            ], 'electrik-config');
        }
    }
}
