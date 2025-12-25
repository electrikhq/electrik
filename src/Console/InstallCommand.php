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
    protected $signature = 'electrik:install {--force : Skip confirmation and use default credentials}';

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

        $force = $this->option('force');
        
        if (!$force) {
            $this->warn('IMPORTANT NOTE');
            $this->warn('1. Electrik is meant to be installed on a fresh Laravel project.');
            $this->warn('2. If you install it on existing project, unwanted issues may happen!');
            $this->warn('3. During installation, Electrik will also delete all existing tables in your database and install a fresh set!');
            
            if (!$this->confirm('Do you wish to continue?')) {
                $this->line('Aborting...');
                return 1;
            }
        }

        // Ask for default user credentials (skip if --force flag is used)
        if ($force) {
            $email = 'hello@example.com';
            $password = 'password';
            $this->line('Using default credentials: ' . $email);
        } else {
            $email = $this->ask('Enter email for default user (or press Enter for hello@example.com)', 'hello@example.com');
            $password = $this->secret('Enter password for default user (or press Enter for "password")');
            if (empty($password)) {
                $password = 'password';
            }
        }

        $this->components->info('Installing Electrik...');

        // Stash any local changes to ensure fresh installation
        $this->components->info('Stashing local changes...');
        $output = [];
        $returnVar = 0;
        exec('cd ' . escapeshellarg(base_path()) . ' && git stash -u 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            $this->line('  ✓ Local changes stashed');
        }

        // Clean composer.json FIRST to remove stale file references before any autoloading
        $this->cleanComposerJson();

        // Copy configuration files
        $this->copyConfigFiles();
        
        // Copy migrations
        $this->copyMigrations();
        
        // Copy models, Livewire components, etc.
        $this->copyApplicationFiles();
        
        // Copy views
        $this->copyViews();
        
        // Copy routes
        $this->copyRoutes();
        
        // Regenerate autoloader after copying all files
        $this->regenerateAutoloader();
        
        // Update Tailwind CSS sources
        $this->updateTailwindSources();
        
        // Update configuration files
        $this->updateConfigurations();
        
        // Publish Spatie Permission migrations
        $this->publishSpatieMigrations();
        
        // Publish Laravel Cashier migrations
        $this->publishCashierMigrations();
        
        // Run migrations
        $this->runMigrations();

        // Create default user and team
        $this->createDefaultUserAndTeam($email, $password);

        // Build frontend assets
        $this->buildAssets();

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
        
        // Copy breadcrumbs config if it exists
        if (File::exists(__DIR__.'/../../config/breadcrumbs.php')) {
            File::copy(__DIR__.'/../../config/breadcrumbs.php', config_path('breadcrumbs.php'));
        }
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
        
        // Copy Helpers (if they exist)
        $filesystem->ensureDirectoryExists(app_path('Helpers'));
        $helpersPath = __DIR__.'/../../src/Helpers';
        if (File::exists($helpersPath) && File::isDirectory($helpersPath)) {
            File::copyDirectory($helpersPath, app_path('Helpers'));
        }
    }

    protected function regenerateAutoloader()
    {
        $this->components->info('Regenerating autoloader...');
        
        $output = [];
        $returnVar = 0;
        exec('cd ' . escapeshellarg(base_path()) . ' && composer dump-autoload --no-interaction 2>&1', $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->line('  ✓ Autoloader regenerated');
            
            // Reload the autoloader class map in the current PHP process
            $classMapFile = base_path('vendor/composer/autoload_classmap.php');
            if (file_exists($classMapFile)) {
                // Get the existing loader instance (already loaded by Laravel)
                $loader = require base_path('vendor/autoload.php');
                
                // Load the newly generated class map
                $newClassMap = require $classMapFile;
                
                // Add the new class map to the existing loader
                if (is_array($newClassMap) && method_exists($loader, 'addClassMap')) {
                    $loader->addClassMap($newClassMap);
                }
            }
        } else {
            $this->warn('  ⚠ Could not regenerate autoloader automatically.');
            $this->warn('  ⚠ Please run: composer dump-autoload');
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

    protected function copyRoutes()
    {
        $this->components->info('Copying routes...');
        
        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists(base_path('routes'));
        
        // Copy breadcrumbs routes if they exist
        if (File::exists(__DIR__.'/../../routes/breadcrumbs.php')) {
            File::copy(__DIR__.'/../../routes/breadcrumbs.php', base_path('routes/breadcrumbs.php'));
        }
    }

    protected function cleanComposerJson()
    {
        $composerJsonPath = base_path('composer.json');
        if (!File::exists($composerJsonPath)) {
            return;
        }
        
        $composer = json_decode(File::get($composerJsonPath), true);
        $changed = false;
        
        // Ensure autoload section exists
        if (!isset($composer['autoload'])) {
            $composer['autoload'] = [];
        }
        
        // Ensure files array exists in autoload
        if (!isset($composer['autoload']['files'])) {
            $composer['autoload']['files'] = [];
        }
        
        // Remove any file references that don't exist
        $originalFiles = $composer['autoload']['files'];
        $composer['autoload']['files'] = array_filter($composer['autoload']['files'], function($path) use (&$changed) {
            $fullPath = base_path($path);
            if (!file_exists($fullPath)) {
                $changed = true;
                return false;
            }
            return true;
        });
        $composer['autoload']['files'] = array_values($composer['autoload']['files']); // Re-index array
        
        // Save if changed
        if ($changed) {
            File::put($composerJsonPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            // Regenerate autoloader immediately to clear stale references
            $output = [];
            $returnVar = 0;
            exec('cd ' . escapeshellarg(base_path()) . ' && composer dump-autoload --no-interaction 2>&1', $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->line('  ✓ Cleaned composer.json and regenerated autoloader');
            } else {
                $this->warn('  ⚠ Could not regenerate autoloader. Please run: composer dump-autoload');
            }
        }
    }

    protected function updateComposerJson()
    {
        $composerJsonPath = base_path('composer.json');
        if (!File::exists($composerJsonPath)) {
            return;
        }
        
        $composer = json_decode(File::get($composerJsonPath), true);
        
        // Ensure autoload section exists
        if (!isset($composer['autoload'])) {
            $composer['autoload'] = [];
        }
        
        // Ensure files array exists in autoload
        if (!isset($composer['autoload']['files'])) {
            $composer['autoload']['files'] = [];
        }
        
        // Add timezones helper if file exists and not already present
        $helperPath = 'app/Helpers/timezones.php';
        if (file_exists(base_path($helperPath)) && !in_array($helperPath, $composer['autoload']['files'])) {
            $composer['autoload']['files'][] = $helperPath;
            File::put($composerJsonPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            // Run composer dump-autoload
            $output = [];
            $returnVar = 0;
            exec('cd ' . escapeshellarg(base_path()) . ' && composer dump-autoload --no-interaction 2>&1', $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->line('  ✓ Helper file registered in composer.json');
            } else {
                $this->warn('  ⚠ Could not run composer dump-autoload automatically.');
                $this->warn('  ⚠ Please run: composer dump-autoload');
            }
        }
    }

    protected function updateTailwindSources()
    {
        $this->components->info('Updating Tailwind CSS sources...');
        
        $appCssPath = resource_path('css/app.css');
        
        if (!File::exists($appCssPath)) {
            $this->warn('  ⚠ app.css not found. Skipping Tailwind sources update.');
            return;
        }
        
        $content = File::get($appCssPath);
        
        // Check if Slate UI source is already added
        if (strpos($content, 'vendor/electrik/slate') !== false) {
            $this->line('  ✓ Slate UI components already in Tailwind sources');
            return;
        }
        
        // Add Slate UI source after Laravel Pagination source
        $slateSource = "@source '../../vendor/electrik/slate/resources/views/**/*.blade.php';";
        
        // Try to add after Laravel Pagination source
        if (strpos($content, '@source \'../../vendor/laravel/framework') !== false) {
            $content = preg_replace(
                '/(@source \'\.\.\/\.\.\/vendor\/laravel\/framework[^\']+\';\s*\n)/',
                '$1' . $slateSource . "\n",
                $content
            );
        } else {
            // If Laravel source not found, add after @import
            $content = preg_replace(
                '/(@import \'tailwindcss\';\s*\n)/',
                '$1' . "\n" . $slateSource . "\n",
                $content
            );
        }
        
        File::put($appCssPath, $content);
        $this->line('  ✓ Added Slate UI components to Tailwind CSS sources');
    }

    protected function updateConfigurations()
    {
        $this->components->info('Updating configurations...');
        
        // Update composer.json to autoload helpers
        $this->updateComposerJson();
        
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
                // Handle both with and without return type
                if (preg_match('/public function boot\(\)(?:\s*:\s*void)?\s*\{/', $content, $matches)) {
                    // Find the boot method and add Cashier configuration
                    $content = preg_replace(
                        '/(public function boot\(\)(?:\s*:\s*void)?\s*\{)([^}]*)(\})/s',
                        '$1$2        \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\Team::class);$3',
                        $content,
                        1
                    );
                } else {
                    // Fallback: add before the closing brace of the class
                    $content = preg_replace(
                        '/(public function boot\(\)(?:\s*:\s*void)?\s*\{[^}]*)(\})/s',
                        '$1        \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\Team::class);$2',
                        $content
                    );
                }
                File::put($appServiceProvider, $content);
            }
        }
    }

    protected function publishSpatieMigrations()
    {
        $this->components->info('Publishing Spatie Permission migrations...');
        
        // Publish Spatie Permission migrations if not already published
        $spatieMigrationsPath = database_path('migrations');
        $hasSpatieMigrations = glob($spatieMigrationsPath.'/*_create_permission_tables.php');
        
        if (empty($hasSpatieMigrations)) {
            // Try publishing without tag first (publishes all resources)
            $this->call('vendor:publish', [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider',
            ]);
            
            // Check again if migrations were published
            $hasSpatieMigrations = glob($spatieMigrationsPath.'/*_create_permission_tables.php');
            if (empty($hasSpatieMigrations)) {
                $this->warn('  ⚠ Spatie Permission migrations could not be published automatically.');
                $this->warn('  ⚠ Please run: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"');
            } else {
                $this->line('  ✓ Spatie Permission migrations published');
            }
        } else {
            $this->line('  ✓ Spatie Permission migrations already published');
        }
    }

    protected function publishCashierMigrations()
    {
        $this->components->info('Publishing Laravel Cashier migrations...');
        
        // Publish Cashier migrations if not already published
        $migrationsPath = database_path('migrations');
        $hasCashierMigrations = glob($migrationsPath.'/*_create_subscriptions_table.php') || 
                                 glob($migrationsPath.'/*_create_subscription_items_table.php');
        
        if (empty($hasCashierMigrations)) {
            try {
                $this->call('vendor:publish', [
                    '--tag' => 'cashier-migrations',
                ]);
                
                // Check again if migrations were published
                $hasCashierMigrations = glob($migrationsPath.'/*_create_subscriptions_table.php') || 
                                         glob($migrationsPath.'/*_create_subscription_items_table.php');
                if (!empty($hasCashierMigrations)) {
                    $this->line('  ✓ Laravel Cashier migrations published');
                } else {
                    $this->warn('  ⚠ Laravel Cashier migrations could not be published automatically.');
                    $this->warn('  ⚠ Please run: php artisan vendor:publish --tag="cashier-migrations"');
                }
            } catch (\Exception $e) {
                $this->warn('  ⚠ Could not publish Cashier migrations: ' . $e->getMessage());
                $this->warn('  ⚠ Please run: php artisan vendor:publish --tag="cashier-migrations"');
            }
        } else {
            $this->line('  ✓ Laravel Cashier migrations already published');
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

    protected function createDefaultUserAndTeam($email, $password)
    {
        $this->components->info('Creating default user and team...');

        try {
            // Ensure autoloader is up to date before checking classes
            $this->regenerateAutoloader();
            
            // Check if actions exist
            if (!class_exists('App\Actions\Auth\CreateUser') || !class_exists('App\Actions\Teams\CreateTeam')) {
                $this->warn('  ⚠ Actions not found. Skipping default user/team creation.');
                return;
            }

            // Check if user already exists
            if (class_exists('App\Models\User')) {
                $existingUser = \App\Models\User::where('email', $email)->first();
                if ($existingUser) {
                    $this->line('  ✓ User already exists with email: ' . $email);
                    return;
                }
            }

            // Create user - use fully qualified class name to avoid autoload issues
            $createUserAction = new \App\Actions\Auth\CreateUser();
            $user = $createUserAction->execute([
                'name' => 'Default User',
                'email' => $email,
                'password' => $password,
                'timezone' => 'UTC',
            ]);

            $this->line('  ✓ Created user: ' . $email);

            // Create team
            $createTeamAction = new \App\Actions\Teams\CreateTeam();
            $team = $createTeamAction->execute($user, 'My Team');

            // Set current team
            $user->current_team_id = $team->id;
            $user->save();
            $user->refresh();

            $this->line('  ✓ Created team: ' . $team->name);
            $this->line('  ✓ Set as current team for user');
            
            // Verify the team was set correctly
            if ($user->currentTeam && $user->currentTeam->id === $team->id) {
                $this->line('  ✓ Verified current team is set correctly');
            } else {
                $this->warn('  ⚠ Warning: Current team may not be set correctly');
            }
        } catch (\Exception $e) {
            $this->warn('  ⚠ Could not create default user/team: ' . $e->getMessage());
        }
    }

    protected function buildAssets()
    {
        $this->components->info('Building frontend assets...');
        
        // Check if package.json exists
        if (!file_exists(base_path('package.json'))) {
            $this->warn('  ⚠ package.json not found. Skipping asset build.');
            return;
        }

        // Check if node_modules exists, if not run npm install first
        if (!is_dir(base_path('node_modules'))) {
            $this->components->info('Installing npm dependencies...');
            $output = [];
            $returnVar = 0;
            exec('cd ' . escapeshellarg(base_path()) . ' && npm install --no-audit --no-fund 2>&1', $output, $returnVar);
            
            if ($returnVar !== 0) {
                $this->warn('  ⚠ Could not install npm dependencies automatically.');
                $this->warn('  ⚠ Please run: npm install && npm run build');
                return;
            }
            $this->line('  ✓ npm dependencies installed');
        }

        // Run npm run build
        $output = [];
        $returnVar = 0;
        exec('cd ' . escapeshellarg(base_path()) . ' && npm run build 2>&1', $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->line('  ✓ Frontend assets built successfully');
        } else {
            $this->warn('  ⚠ Could not build frontend assets automatically.');
            $this->warn('  ⚠ Please run: npm run build');
        }
    }
}

