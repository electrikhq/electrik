<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class Install extends Command {
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

        // Tailwind / Vite...
        copy(__DIR__.'/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__.'/../../stubs/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../stubs/resources/css/application.css', resource_path('css/application.css'));
        copy(__DIR__.'/../../stubs/resources/js/application.js', resource_path('js/application.js'));


        // Configuration...
		// copy(__DIR__.'/../../config/permission.php', config_path('permission.php'));
		// copy(__DIR__.'/../../config/livewire.php', config_path('livewire.php'));
		// copy(__DIR__.'/../../config/electrik.php', config_path('electrik.php'));
		// copy(__DIR__.'/../../config/plans.php', config_path('plans.php'));


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

		$this->runCommands(['php artisan vendor:publish --provider="Mpociot\Teamwork\TeamworkServiceProvider"']);
		$this->runCommands(['php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"']);
		$this->runCommands(['php artisan vendor:publish --tag="cashier-migrations"']);


		$timestamp = date('Y_m_d_His', time());

		copy(__DIR__.'/../../database/migrations/2022_09_29_000000_add_cols_to_users_table.php', database_path('migrations/'.$timestamp.'_add_cols_to_users_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_000001_create_customer_columns.php', database_path('migrations/'.$timestamp.'_create_customer_columns.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_000002_update_subscriptions_table.php', database_path('migrations/'.$timestamp.'_update_subscriptions_table.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_063626_create_configurations_tables.php', database_path('migrations/'.$timestamp.'_create_configurations_tables.php'));
		copy(__DIR__.'/../../database/migrations/2022_09_29_195017_create_addresses_table.php', database_path('migrations/'.$timestamp.'_create_addresses_table.php'));

		$this->runCommands(['npm install', 'npm run build']);

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

}
