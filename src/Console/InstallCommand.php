<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'electrik:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Electrik resources';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('
    ________          __       _ __  
   / ____/ /__  _____/ /______(_) /__
  / __/ / / _ \/ ___/ __/ ___/ / //_/
 / /___/ /  __/ /__/ /_/ /  / / ,<   
/_____/_/\___/\___/\__/_/  /_/_/|_|  
        ');

        $this->warn('IMPORTANT NOTE');
        $this->warn('1. Electrik is meant to be installed on a fresh Laravel project.');
        $this->warn('2. If you install it on existing project, unwanted issues may happen!');
        $this->warn('3. During installation, Electrik will also delete all existing tables in your database and install a fresh set!');
        
        if (!$this->confirm('Do you wish to continue?')) {
            $this->line('Aborting...');
            return 1;
        }

        $this->components->info('Installing Electrik...');

        // Copy configuration files
        $this->copyConfigFiles();
        
        // Copy migrations
        $this->copyMigrations();
        
        // Copy models, Livewire components, etc.
        $this->copyApplicationFiles();
        
        // Copy views
        $this->copyViews();
        
        // Update configuration files
        $this->updateConfigurations();
        
        // Run migrations
        $this->runMigrations();

        $this->line('');
        $this->components->info('Electrik installed successfully.');
        $this->components->warn('Note: Do not forget to update the following:');
        $this->components->warn('1. electrik.php in config folder');
        $this->components->warn('2. Stripe keys in your .env file');
        $this->components->warn('3. Run "php artisan electrik:stripe:sync" to sync plans from Stripe');

        return 0;
    }

    protected function copyConfigFiles()
    {
        $this->components->info('Copying configuration files...');
        
        File::copy(__DIR__.'/../../config/electrik.php', config_path('electrik.php'));
    }

    protected function copyMigrations()
    {
        $this->components->info('Copying migrations...');
        
        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists(database_path('migrations'));
        
        // Copy all migrations from package (if they exist)
        if (File::exists(__DIR__.'/../../database/migrations') && File::isDirectory(__DIR__.'/../../database/migrations')) {
            File::copyDirectory(
                __DIR__.'/../../database/migrations',
                database_path('migrations')
            );
        } else {
            $this->warn('  ⚠ No migrations found in package. Skipping migration copy.');
        }
    }

    protected function copyApplicationFiles()
    {
        $this->components->info('Copying application files...');
        
        $filesystem = new Filesystem;
        
        // Copy models (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Models'));
        if (File::exists(__DIR__.'/../Models') && File::isDirectory(__DIR__.'/../Models')) {
            File::copyDirectory(__DIR__.'/../Models', app_path('Models'));
        }
        
        // Copy Livewire components (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Livewire'));
        if (File::exists(__DIR__.'/../Livewire') && File::isDirectory(__DIR__.'/../Livewire')) {
            File::copyDirectory(__DIR__.'/../Livewire', app_path('Livewire'));
        }
        
        // Copy Actions (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Actions'));
        if (File::exists(__DIR__.'/../Actions') && File::isDirectory(__DIR__.'/../Actions')) {
            File::copyDirectory(__DIR__.'/../Actions', app_path('Actions'));
        }
        
        // Copy Events (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Events'));
        if (File::exists(__DIR__.'/../Events') && File::isDirectory(__DIR__.'/../Events')) {
            File::copyDirectory(__DIR__.'/../Events', app_path('Events'));
        }
        
        // Copy Listeners (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Listeners'));
        if (File::exists(__DIR__.'/../Listeners') && File::isDirectory(__DIR__.'/../Listeners')) {
            File::copyDirectory(__DIR__.'/../Listeners', app_path('Listeners'));
        }
        
        // Copy Services (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Services'));
        if (File::exists(__DIR__.'/../Services') && File::isDirectory(__DIR__.'/../Services')) {
            File::copyDirectory(__DIR__.'/../Services', app_path('Services'));
        }
        
        // Copy Repositories (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Repositories'));
        if (File::exists(__DIR__.'/../Repositories') && File::isDirectory(__DIR__.'/../Repositories')) {
            File::copyDirectory(__DIR__.'/../Repositories', app_path('Repositories'));
        }
        
        // Copy Requests (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Http/Requests'));
        if (File::exists(__DIR__.'/../Requests') && File::isDirectory(__DIR__.'/../Requests')) {
            File::copyDirectory(__DIR__.'/../Requests', app_path('Http/Requests'));
        }
        
        // Copy Notifications (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Notifications'));
        if (File::exists(__DIR__.'/../Notifications') && File::isDirectory(__DIR__.'/../Notifications')) {
            File::copyDirectory(__DIR__.'/../Notifications', app_path('Notifications'));
        }
        
        // Copy Traits (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Traits'));
        if (File::exists(__DIR__.'/../Traits') && File::isDirectory(__DIR__.'/../Traits')) {
            File::copyDirectory(__DIR__.'/../Traits', app_path('Traits'));
        }
    }

    protected function copyViews()
    {
        $this->components->info('Copying views...');
        
        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists(resource_path('views'));
        
        // Copy views (if they exist)
        if (File::exists(__DIR__.'/../../resources/views') && File::isDirectory(__DIR__.'/../../resources/views')) {
            File::copyDirectory(__DIR__.'/../../resources/views', resource_path('views'));
        } else {
            $this->warn('  ⚠ No views found in package. Skipping view copy.');
        }
    }

    protected function updateConfigurations()
    {
        $this->components->info('Updating configurations...');
        
        // Update permission config
        $permissionConfig = config_path('permission.php');
        if (File::exists($permissionConfig)) {
            $content = File::get($permissionConfig);
            $content = str_replace(
                "'permission' => Spatie\Permission\Models\Permission::class,",
                "'permission' => App\Models\Permission::class,",
                $content
            );
            $content = str_replace(
                "'role' => Spatie\Permission\Models\Role::class,",
                "'role' => App\Models\Role::class,",
                $content
            );
            $content = str_replace(
                "'teams' => false,",
                "'teams' => true,",
                $content
            );
            File::put($permissionConfig, $content);
        }
        
        // Update teamwork config
        $teamworkConfig = config_path('teamwork.php');
        if (File::exists($teamworkConfig)) {
            $content = File::get($teamworkConfig);
            $content = str_replace(
                "'user_model' => config('auth.providers.users.model', App\User::class),",
                "'user_model' => config('auth.providers.users.model', App\Models\User::class),",
                $content
            );
            $content = str_replace(
                "'team_model' => Mpociot\Teamwork\TeamworkTeam::class,",
                "'team_model' => App\Models\Team::class,",
                $content
            );
            $content = str_replace(
                "'invite_model' => Mpociot\Teamwork\TeamInvite::class,",
                "'invite_model' => App\Models\TeamInvite::class,",
                $content
            );
            File::put($teamworkConfig, $content);
        }
        
        // Update livewire config
        $livewireConfig = config_path('livewire.php');
        if (File::exists($livewireConfig)) {
            $content = File::get($livewireConfig);
            $content = str_replace(
                "'layout' => 'components.layouts.app',",
                "'layout' => 'layouts.livewire.app',",
                $content
            );
            File::put($livewireConfig, $content);
        }
        
        // Update AppServiceProvider for Cashier
        $appServiceProvider = app_path('Providers/AppServiceProvider.php');
        if (File::exists($appServiceProvider)) {
            $content = File::get($appServiceProvider);
            if (strpos($content, 'Cashier::useCustomerModel') === false) {
                $content = str_replace(
                    'public function boot()',
                    "public function boot()\n    {\n        \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\Team::class);\n    }",
                    $content
                );
                File::put($appServiceProvider, $content);
            }
        }
    }

    protected function runMigrations()
    {
        $this->components->info('Running migrations...');
        
        // Only run migrations if there are any
        $migrationPath = database_path('migrations');
        $migrations = glob($migrationPath.'/*_*.php');
        
        if (empty($migrations)) {
            $this->warn('  ⚠ No migrations found. Skipping migration run.');
            return;
        }
        
        $this->call('migrate:fresh');
    }
}

