<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;  
use Illuminate\Filesystem\Filesystem;



class InstallCommand extends Command {
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
    protected $description = 'Installs Electrik resources';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {

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
		}

		$this->components->info('Installing Electrik...');

        copy(__DIR__.'/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
		/* .js is not working with vite 4.3.9. workaround ref: https://github.com/BuilderIO/qwik/issues/836 */
        copy(__DIR__.'/../../stubs/postcss.config.js', base_path('postcss.config.cjs'));
        copy(__DIR__.'/../../stubs/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../stubs/resources/css/application.css', resource_path('css/application.css'));
        copy(__DIR__.'/../../stubs/resources/js/application.js', resource_path('js/application.js'));

		$this->components->info('Installed Configurations.');

		$this->requireComposerPackages([
			"mpociot/teamwork:^8.1",
			"spatie/laravel-permission:^5.10",
			"usernotnull/tall-toasts:^1.7",
			"wire-elements/modal:^1.0",
			"laravel/cashier:^14.12",
			"livewire/livewire:^2.12",
			"rappasoft/laravel-livewire-tables:^2.14",
			"electrik/slate:^0.1",
			"doctrine/dbal:^3.6",
		]);

		$this->components->info('Installed Composer Packages.');

		$this->updateNodePackages(function ($packages) {
            return [
				"@tailwindcss/forms" => "^0.5.2",
				"@tailwindcss/typography" => "^0.5.7",
				"alpinejs" => "^3.4.2",
				"autoprefixer" => "^10.4.2",
				"postcss" => "^8.4.6",
				"tailwindcss" => "^3.1.0",
				"tippy.js" => "^6.3.7",
				"@alpinejs/collapse" => "^3.10.3",
				
            ] + $packages;
        });

		$this->components->info('Installed Node Packages.');

		$this->runCommands(['php artisan vendor:publish --provider="Mpociot\Teamwork\TeamworkServiceProvider"']);
		$this->runCommands(['php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"']);
		$this->runCommands(['php artisan vendor:publish --tag="cashier-migrations"']);
		$this->runCommands(['php artisan vendor:publish --tag="cashier-config"']);
		$this->runCommands(['php artisan livewire:publish --config']);
		
		File::copyDirectory(__DIR__.'/../Models/', app_path('Models'));
		
		(new Filesystem)->ensureDirectoryExists(app_path('Http/Livewire'));
		File::copyDirectory(__DIR__.'/../Http/Livewire', app_path('Http/Livewire'));
		
		(new Filesystem)->ensureDirectoryExists(app_path('Listeners'));
		File::copyDirectory(__DIR__.'/../Listeners', app_path('Listeners'));
		
		(new Filesystem)->ensureDirectoryExists(app_path('Notifications'));
		File::copyDirectory(__DIR__.'/../Notifications', app_path('Notifications'));
		
		(new Filesystem)->ensureDirectoryExists(app_path('Traits'));
		File::copyDirectory(__DIR__.'/../Traits', app_path('Traits'));

		File::copyDirectory(__DIR__.'/../../resources/views/vendor/', resource_path('views/vendor'));
		File::copyDirectory(__DIR__.'/../../resources/views/includes', resource_path('views/includes'));
		File::copyDirectory(__DIR__.'/../../resources/views/layouts', resource_path('views/layouts'));
		File::copyDirectory(__DIR__.'/../../resources/views/livewire', resource_path('views/livewire'));
		
		File::copy(__DIR__.'/../../config/plans.php', base_path().'/config/plans.php');
		File::copy(__DIR__.'/../../config/electrik.php', base_path().'/config/electrik.php');
		File::copy(__DIR__.'/../../routes/web.php', base_path().'/routes/web.php');

		$this->components->info('Published third-party package migrations and assets.');


		$this->replaceInFile("'permission' => Spatie\Permission\Models\Permission::class,", "'permission' => App\Models\Permission::class,", config_path('permission.php'));
		$this->replaceInFile("'role' => Spatie\Permission\Models\Role::class,", "'role' => App\Models\Role::class,", config_path('permission.php'));
		$this->replaceInFile("'user_model' => config('auth.providers.users.model', App\User::class),", "'user_model' => config('auth.providers.users.model', App\Models\User::class),", config_path('teamwork.php'));
		$this->replaceInFile("'team_model' => Mpociot\Teamwork\TeamworkTeam::class,", "'team_model' => App\Models\Team::class,", config_path('teamwork.php'));
		$this->replaceInFile("'invite_model' => Mpociot\Teamwork\TeamInvite::class,", "'invite_model' => App\Models\TeamInvite::class,", config_path('teamwork.php'));
		$this->replaceInFile("'layout' => 'layouts.app',", "'layout' => 'layouts.livewire.app',", config_path('livewire.php'));
		$this->replaceInFile("'teams' => false,", "'teams' => true,", config_path('permission.php'));

		$stripeKey = $this->ask('Please enter your stripe key');
		$stripeSecret = $this->ask('Please enter your stripe secret');


file_put_contents(base_path().'/.env',
<<<EOF

STRIPE_KEY=$stripeKey
STRIPE_SECRET=$stripeSecret

EOF, FILE_APPEND);

file_put_contents(app_path().'/Providers/AppServiceProvider.php',
<<<EOF
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use App\Models\Team;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		Cashier::useCustomerModel(Team::class);
    }
}
EOF);

		$timestamp = date('Y_m_d_His', time());

		/* added x prefix to make sure our migrations run at the end */
		copy(__DIR__.'/../../database/migrations/2022_09_29_000000_add_cols_to_users_table.php', database_path('migrations/'.$timestamp.'_xx_add_cols_to_users_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_000001_create_customer_columns.php', database_path('migrations/'.$timestamp.'_xx_create_customer_columns.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_000002_update_subscriptions_table.php', database_path('migrations/'.$timestamp.'_xx_update_subscriptions_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_063626_create_configurations_tables.php', database_path('migrations/'.$timestamp.'_xx_create_configurations_tables.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_195017_create_addresses_table.php', database_path('migrations/'.$timestamp.'_xx_create_addresses_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_090000_add_cols_to_team_invites_table.php', database_path('migrations/'.$timestamp.'_xx_add_cols_to_team_invites_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_10_02_1950170_add_display_names_to_roles_and_permissions.php', database_path('migrations/'.$timestamp.'_xx_add_display_names_to_roles_and_permissions.php'));

		$this->components->info('Published Electrik migrations.');

		
		$this->runCommands(['npm install', 'npm run build']);
		
		$this->components->info('Built Electrik assets.');
		$this->runCommands(['php artisan migrate:fresh']);
        $this->components->info('Database installed.');
		
        $this->line('');
        $this->components->warn('Note: Do not forget to update the following for this app to run properly:');
        $this->components->warn('1. electrik.php and plans.php in config folder');
        $this->components->warn('2. CASHIER keys in your .env file');
        $this->line('');
		$this->components->info('Electrik installed successfully.');
        
        return 0;
    }

	protected function updateNodePackages(callable $callback, $dev = true) {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

	/**
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return void
     */
    protected function requireComposerPackages($packages) {


		$command = ['composer', 'require'];

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

	protected function runCommands($commands) {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

	/**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path) {

        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));

    }


}

