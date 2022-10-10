<?php

namespace Electrik;

use Electrik\Http\Livewire\HelloWorld;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use ReflectionClass;
use Illuminate\Support\Str;
use SplFileInfo;

class ElectrikServiceProvider extends ServiceProvider {
    
	/**
	 * Bootstrap any package services.
	 *
	 * @return void
	 */
	public function boot() {


		if (class_exists(Livewire::class)) {

			$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
			$this->loadViewsFrom(__DIR__.'/../resources/views', 'electrik');
			// $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

			$this->registerLivewireComponentDirectory(__DIR__ . '/Http/Livewire', 'Electrik\\Http\\Livewire', '');

		};

		if ($this->app->runningInConsole()) {
			// Export the migration
				
			$timestamp = date('Y_m_d_His', time());


			$this->publishes([
				__DIR__.'/../database/migrations/2022_09_29_000000_add_cols_to_users_table.php' => database_path('migrations/'.$timestamp.'_add_cols_to_users_table.php'),
				__DIR__.'/../database/migrations/2022_09_29_000001_create_customer_columns' => database_path('migrations/'.$timestamp.'_create_customer_columns.php'),
				__DIR__.'/../database/migrations/2022_09_29_000002_update_subscriptions_table' => database_path('migrations/'.$timestamp.'_update_subscriptions_table.php'),
				__DIR__.'/../database/migrations/2022_09_29_063626_create_configurations_tables' => database_path('migrations/'.$timestamp.'_create_configurations_tables.php'),
				__DIR__.'/../database/migrations/2022_09_29_195017_create_addresses_table' => database_path('migrations/'.$timestamp.'_create_addresses_table.php'),

				// you can add any number of migrations here
			], 'migrations');


			$this->commands([
				\Electrik\Console\InstallCommand::class,
				\Electrik\Console\MakeCommand::class,
			]);

		}

	}

	public function register() {

		// $this->mergeConfigFrom(__DIR__.'/../config/livewire.php', 'livewire');
		// $this->mergeConfigFrom(__DIR__.'/../config/permission.php', 'permission');
		// $this->mergeConfigFrom(__DIR__.'/../config/teamwork.php', 'teamwork');
		// $this->mergeConfigFrom(__DIR__.'/../config/auth.php', 'auth.providers');
		$this->mergeConfigFrom(__DIR__.'/../config/plans.php', 'plans');
		// $route->get('user', function() {
			// dd(config('permission'));
		// });
	}

	//  /**
    //  * Merge the given configuration with the existing configuration.
    //  *
    //  * @param  string  $path
    //  * @param  string  $key
    //  * @return void
    //  */
    // protected function mergeConfigFrom($path, $key)
    // {
    //     $config = $this->app['config']->get($key, []);

    //     $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    // }

    // /**
    //  * Merges the configs together and takes multi-dimensional arrays into account.
    //  *
    //  * @param  array  $original
    //  * @param  array  $merging
    //  * @return array
    //  */
    // protected function mergeConfig(array $original, array $merging)
    // {
    //     $array = array_merge($original, $merging);

    //     foreach ($original as $key => $value) {
    //         if (! is_array($value)) {
    //             continue;
    //         }

    //         if (! Arr::exists($merging, $key)) {
    //             continue;
    //         }

    //         if (is_numeric($key)) {
    //             continue;
    //         }

    //         $array[$key] = $this->mergeConfig($value, $merging[$key]);
    //     }

    //     return $array;
    // }

	// protected function mergeConfigFrom($path, $key)
    // {
    //     $config = $this->app['config']->get($key, []);

    //     $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    // }

    // /**
    //  * Merges the configs together and takes multi-dimensional arrays into account.
    //  *
    //  * @param  array  $original
    //  * @param  array  $merging
    //  * @return array
    //  */
    // protected function mergeConfig(array $original, array $merging)
    // {
    //     $array = array_merge($original, $merging);

    //     foreach ($original as $key => $value) {
    //         if (! is_array($value)) {
    //             continue;
    //         }

    //         if (! Arr::exists($merging, $key)) {
    //             continue;
    //         }

    //         if (is_numeric($key)) {
    //             continue;
    //         }

    //         $array[$key] = $this->mergeConfig($value, $merging[$key]);
    //     }

    //     return $array;
    // }
	

	protected function registerLivewireComponentDirectory($directory, $namespace, string $aliasPrefix = '') {

        collect((new Filesystem)->allFiles($directory))
            ->map(function (SplFileInfo $file) use ($namespace) {
                return (string) Str::of($namespace)
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            // ->filter(function ($class) {
            //     return is_subclass_of($class, Component::class) && ! (new ReflectionClass($class))->isAbstract();
            // })
            ->each(function ($class) use ($namespace, $aliasPrefix) {
                $alias = Str::of($class)
                    ->after($namespace . '\\')
                    ->replace(['/', '\\'], '.')
                    ->prepend($aliasPrefix)
                    ->explode('.')
                    ->map([Str::class, 'kebab'])
                    ->implode('.');

                Livewire::component($alias, $class);
            });
	}


}