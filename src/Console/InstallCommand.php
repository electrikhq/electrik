<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;  


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

		$this->components->info('Installing Electrik...');

        // Tailwind / Vite...
        copy(__DIR__.'/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__.'/../../stubs/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../stubs/resources/css/application.css', resource_path('css/application.css'));
        copy(__DIR__.'/../../stubs/resources/js/application.js', resource_path('js/application.js'));

		$this->components->info('Installed Configurations.');

		$this->requireComposerPackages([
			"mpociot/teamwork:^7.0",
			"spatie/laravel-permission:^5.5",
			"usernotnull/tall-toasts:^1.5",
			"wire-elements/modal:^1.0",
			"laravel/cashier:^14.1",
			"livewire/livewire:^2.10",
			"rappasoft/laravel-livewire-tables:^2.8",
			"neerajsohal/slate:dev-development",
			"doctrine/dbal:^3.4",
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
		
		File::copyDirectory(__DIR__.'/../models/', app_path('models'));

		File::copyDirectory(__DIR__.'/../../resources/views/vendor/', resource_path('views/vendor'));

		$this->components->info('Published third-party package migrations and assets.');


		$this->replaceInFile("'model' => App\Models\User::class,", "'model' => Electrik\Models\User::class,", config_path('auth.php'));
		$this->replaceInFile("'permission' => Spatie\Permission\Models\Permission::class,", "'permission' => Electrik\Models\Permission::class,", config_path('permission.php'));
		$this->replaceInFile("'role' => Spatie\Permission\Models\Role::class,", "'role' => Electrik\Models\Role::class,", config_path('permission.php'));
		$this->replaceInFile("'user_model' => config('auth.providers.users.model', App\User::class),", "'user_model' => config('auth.providers.users.model', Electrik\Models\User::class),", config_path('teamwork.php'));
		$this->replaceInFile("'team_model' => Mpociot\Teamwork\TeamworkTeam::class,", "'team_model' => Electrik\Models\Team::class,", config_path('teamwork.php'));
		$this->replaceInFile("'invite_model' => Mpociot\Teamwork\TeamInvite::class,", "'invite_model' => Electrik\Models\TeamInvite::class,", config_path('teamwork.php'));
		$this->replaceInFile("'class_namespace' => 'App\\Http\\Livewire',", "'class_namespace' => 'Electrik\\Http\\Livewire',", config_path('livewire.php'));
		$this->replaceInFile("'layout' => 'layouts.app',", "'layout' => 'electrik::layouts.livewire.app',", config_path('livewire.php'));
		$this->replaceInFile("'teams' => false,", "'teams' => true,", config_path('permission.php'));

		file_put_contents(app_path() . '/../routes/web.php', <<<EOF
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return redirect()->route('dashboard.index');
});
EOF);

file_put_contents(base_path().'/.env',
<<<EOF

STRIPE_KEY=
STRIPE_SECRET=

CASHIER_TAX_RATE_SGST=
CASHIER_TAX_RATE_CGST=
CASHIER_TAX_RATE_IGST=

EOF, FILE_APPEND);
file_put_contents(app_path().'/Providers/AppServiceProvider.php',
<<<EOF
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Electrik\Models\Team;

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

		// sleep(3);

		/* added x prefix to make sure our migrations run at the end */
		copy(__DIR__.'/../../database/migrations/2022_09_29_000000_add_cols_to_users_table.php', database_path('migrations/'.$timestamp.'_xx_add_cols_to_users_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_000001_create_customer_columns.php', database_path('migrations/'.$timestamp.'_xx_create_customer_columns.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_000002_update_subscriptions_table.php', database_path('migrations/'.$timestamp.'_xx_update_subscriptions_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_063626_create_configurations_tables.php', database_path('migrations/'.$timestamp.'_xx_create_configurations_tables.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_195017_create_addresses_table.php', database_path('migrations/'.$timestamp.'_xx_create_addresses_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_090000_add_cols_to_team_invites_table.php', database_path('migrations/'.$timestamp.'_xx_add_cols_to_team_invites_table.php'));

		$this->components->info('Published Electrik migrations.');

		$this->runCommands(['npm install --dev', 'npm run build']);

		$this->components->info('Built Electrik assets.');

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

