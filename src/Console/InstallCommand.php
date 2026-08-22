<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class InstallCommand extends Command
{
    protected $signature = 'electrik:install
                            {--force : Overwrite published config if it exists}
                            {--migrate : Run migrations after install}';

    protected $description = 'Install Electrik into a Laravel application';

    public function handle(): int
    {
        $this->publishConfig();
        $this->components->info('Installing Electrik '.$this->electrikVersion());

        $this->ensureSlateCssImport();
        $this->ensureTeamwork();
        $this->ensurePermissionTeams();
        $this->ensureUserModel();
        $this->ensureSanctum();
        $this->ensureCashier();
        $this->ensureSessionDriver();
        $this->ensureStripeEnv();
        $this->ensureStorageLink();
        $this->printStripeWebhookHelp();

        if ($this->option('migrate')) {
            $this->call('migrate', ['--force' => true]);
            $this->call('electrik:permissions:sync', ['--teams' => true]);
        }

        $this->newLine();
        $this->components->info('Electrik is ready.');
        $this->line('  Auth:     /login /register /forgot-password');
        $this->line('  Teams:    /teams (via electrik/teamwork)');
        $this->line('  Billing:  /billing  — set STRIPE_* then electrik:stripe:sync');
        $this->line('  Onboard:  /onboarding (disable with ELECTRIK_ONBOARDING=false)');
        $this->line('  Demo:     php artisan electrik:seed-demo');
        $this->line('  Storage:  php artisan storage:link  (avatars & uploads)');
        $this->line('  Migrate:  php artisan migrate   (or --migrate)');
        $this->line('  UI:       <x-slate::*> — https://slate.electrik.dev');

        return self::SUCCESS;
    }

    protected function electrikVersion(): string
    {
        return (string) config('electrik.version', '5.x');
    }

    protected function publishConfig(): void
    {
        $target = config_path('electrik.php');

        if (File::exists($target) && ! $this->option('force')) {
            $this->components->twoColumnDetail('config/electrik.php', 'exists');

            return;
        }

        $this->callSilent('vendor:publish', [
            '--tag' => 'electrik-config',
            '--force' => true,
        ]);

        $this->components->twoColumnDetail('config/electrik.php', 'published');
    }

    protected function ensureSlateCssImport(): void
    {
        $cssPath = resource_path('css/app.css');

        if (! File::exists($cssPath)) {
            $this->components->warn('resources/css/app.css not found; skip Slate CSS wiring.');

            return;
        }

        $contents = File::get($cssPath);
        $changed = false;

        $slateImport = "@import '../../vendor/electrik/slate/resources/css/slate.css';";
        if (! str_contains($contents, 'electrik/slate/resources/css/slate.css')) {
            if (preg_match('/@import\s+[\'"]tailwindcss[\'"]\s*;/', $contents)) {
                $contents = preg_replace(
                    '/(@import\s+[\'"]tailwindcss[\'"]\s*;)/',
                    "$1\n".$slateImport,
                    $contents,
                    1
                );
            } else {
                $contents = $slateImport."\n".$contents;
            }
            $changed = true;
        }

        if (! str_contains($contents, 'vendor/electrik/slate/resources/views')) {
            $contents = rtrim($contents)."\n\n@source '../../vendor/electrik/slate/resources/views';\n";
            $changed = true;
        }

        if (! str_contains($contents, 'vendor/electrik/electrik/resources/views')) {
            $contents = rtrim($contents)."\n@source '../../vendor/electrik/electrik/resources/views';\n";
            $changed = true;
        }

        if ($changed) {
            File::put($cssPath, $contents);
            $this->components->twoColumnDetail('resources/css/app.css', 'Slate + Electrik @source');
        } else {
            $this->components->twoColumnDetail('resources/css/app.css', 'already wired');
        }
    }

    protected function ensureTeamwork(): void
    {
        if (! class_exists(\Mpociot\Teamwork\TeamworkServiceProvider::class)) {
            $this->components->warn('electrik/teamwork missing; skip Teamwork setup.');

            return;
        }

        if (! File::exists(config_path('teamwork.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Mpociot\\Teamwork\\TeamworkServiceProvider',
            ]);
            $this->components->twoColumnDetail('teamwork config/migrations', 'published');
        } else {
            $this->components->twoColumnDetail('teamwork config', 'exists');
        }

        $path = config_path('teamwork.php');
        if (File::exists($path)) {
            $contents = File::get($path);
            $contents = preg_replace(
                "/'team_model'\\s*=>\\s*[^,\\n]+/",
                "'team_model' => \\Electrik\\Models\\Team::class",
                $contents,
                1
            ) ?? $contents;
            File::put($path, $contents);
            $this->components->twoColumnDetail('teamwork.team_model', 'Electrik\\Models\\Team');
        }
    }

    protected function ensurePermissionTeams(): void
    {
        if (! class_exists(\Spatie\Permission\PermissionServiceProvider::class)) {
            $this->components->warn('spatie/laravel-permission missing; skip permission setup.');

            return;
        }

        if (! File::exists(config_path('permission.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => "Spatie\\Permission\\PermissionServiceProvider",
            ]);
            $this->components->twoColumnDetail('permission config/migrations', 'published');
        } else {
            $this->components->twoColumnDetail('permission config', 'exists');
        }

        $path = config_path('permission.php');
        if (File::exists($path)) {
            $contents = File::get($path);
            if (preg_match("/'teams'\\s*=>\\s*false/", $contents)) {
                $contents = preg_replace("/'teams'\\s*=>\\s*false/", "'teams' => true", $contents, 1);
                File::put($path, $contents);
                $this->components->twoColumnDetail('permission.teams', 'enabled');
            } else {
                $this->components->twoColumnDetail('permission.teams', 'already on or custom');
            }
        }

        config(['permission.teams' => true]);
        $this->callSilent('config:clear');
        config(['permission.teams' => true]);

        $path = config_path('permission.php');
        if (File::exists($path)) {
            $contents = File::get($path);
            $contents = str_replace(
                'use Spatie\\Permission\\Models\\Permission;',
                'use Electrik\\Models\\Permission;',
                $contents
            );
            $contents = str_replace(
                'use Spatie\\Permission\\Models\\Role;',
                'use Electrik\\Models\\Role;',
                $contents
            );
            File::put($path, $contents);
            $this->components->twoColumnDetail('permission models', 'Electrik Role/Permission');
        }

        config([
            'permission.models.permission' => \Electrik\Models\Permission::class,
            'permission.models.role' => \Electrik\Models\Role::class,
        ]);
    }

    protected function ensureUserModel(): void
    {
        $model = config('auth.providers.users.model');

        if (! is_string($model) || ! class_exists($model)) {
            $this->components->warn('User model not found; wire traits manually.');

            return;
        }

        try {
            $path = (new ReflectionClass($model))->getFileName();
        } catch (\ReflectionException) {
            $this->components->warn('Could not locate User model file.');

            return;
        }

        if (! $path || ! File::isWritable($path)) {
            $this->components->warn('User model is not writable.');

            return;
        }

        $contents = File::get($path);
        $original = $contents;

        if (config('electrik.auth.email_verification', true)) {
            if (str_contains($contents, '// use Illuminate\\Contracts\\Auth\\MustVerifyEmail;')) {
                $contents = str_replace(
                    '// use Illuminate\\Contracts\\Auth\\MustVerifyEmail;',
                    'use Illuminate\\Contracts\\Auth\\MustVerifyEmail;',
                    $contents
                );
            } elseif (! preg_match('/^use Illuminate\\\\Contracts\\\\Auth\\\\MustVerifyEmail;/m', $contents)) {
                $contents = preg_replace(
                    '/(namespace App\\\\Models;\\s+)/',
                    "$1\nuse Illuminate\\Contracts\\Auth\\MustVerifyEmail;\n",
                    $contents,
                    1
                ) ?? $contents;
            }

            if (! preg_match('/class\s+User\s+extends\s+\w+\s+implements\s+[^{]*MustVerifyEmail/', $contents)) {
                $contents = preg_replace(
                    '/class\s+User\s+extends\s+(\w+)/',
                    'class User extends $1 implements MustVerifyEmail',
                    $contents,
                    1
                ) ?? $contents;
            }
        }

        $imports = [
            'Mpociot\\Teamwork\\Traits\\UserHasTeams' => 'use Mpociot\\Teamwork\\Traits\\UserHasTeams;',
            'Spatie\\Permission\\Traits\\HasRoles' => 'use Spatie\\Permission\\Traits\\HasRoles;',
        ];

        foreach ($imports as $fqcn => $useLine) {
            if (! str_contains($contents, $fqcn)) {
                $contents = preg_replace(
                    '/(namespace App\\\\Models;\\s+)/',
                    "$1\n".$useLine."\n",
                    $contents,
                    1
                ) ?? $contents;
            }
        }

        // Drop old Electrik HasTeams if present
        $contents = str_replace("use Electrik\\Concerns\\HasTeams;\n", '', $contents);
        $contents = str_replace('HasTeams, ', '', $contents);
        $contents = str_replace(', HasTeams', '', $contents);

        if (preg_match('/use\s+HasFactory,\s*HasRoles,\s*Notifiable;/', $contents)) {
            $contents = preg_replace(
                '/use\s+HasFactory,\s*HasRoles,\s*Notifiable;/',
                'use HasFactory, HasRoles, Notifiable, UserHasTeams;',
                $contents,
                1
            ) ?? $contents;
        } elseif (preg_match('/use\s+HasFactory,\s*Notifiable;/', $contents)) {
            $contents = preg_replace(
                '/use\s+HasFactory,\s*Notifiable;/',
                'use HasFactory, HasRoles, Notifiable, UserHasTeams;',
                $contents,
                1
            ) ?? $contents;
        } elseif (! str_contains($contents, 'UserHasTeams')) {
            $contents = preg_replace(
                '/(class\s+User\s+extends\s+[^{]+\{)/',
                "$1\n    use HasRoles, UserHasTeams;\n",
                $contents,
                1
            ) ?? $contents;
        }

        if ($contents !== $original) {
            File::put($path, $contents);
            $this->components->twoColumnDetail('User model', 'UserHasTeams + HasRoles (+ MustVerifyEmail)');
        } else {
            $this->components->twoColumnDetail('User model', 'already wired');
        }
    }

    protected function ensureSanctum(): void
    {
        if (! class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
            $this->components->warn('laravel/sanctum missing; skip API token setup.');

            return;
        }

        if (! File::exists(config_path('sanctum.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Laravel\\Sanctum\\SanctumServiceProvider',
            ]);
            $this->components->twoColumnDetail('sanctum config/migrations', 'published');
        } else {
            $this->components->twoColumnDetail('sanctum config', 'exists');
        }

        $model = config('auth.providers.users.model');

        if (! is_string($model) || ! class_exists($model)) {
            return;
        }

        try {
            $path = (new ReflectionClass($model))->getFileName();
        } catch (\ReflectionException) {
            return;
        }

        if (! $path || ! File::isWritable($path)) {
            return;
        }

        $contents = File::get($path);
        $original = $contents;

        if (! str_contains($contents, 'Laravel\\Sanctum\\HasApiTokens')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Laravel\\Sanctum\\HasApiTokens;\n",
                $contents,
                1
            ) ?? $contents;
        }

        if (! str_contains($contents, 'HasApiTokens')) {
            $contents = preg_replace(
                '/use HasFactory, HasRoles, Notifiable, UserHasTeams;/',
                'use HasApiTokens, HasFactory, HasRoles, Notifiable, UserHasTeams;',
                $contents,
                1
            ) ?? $contents;
        }

        if ($contents !== $original) {
            File::put($path, $contents);
            $this->components->twoColumnDetail('User model', 'HasApiTokens added');
        }
    }

    protected function ensureCashier(): void
    {
        if (! class_exists(\Laravel\Cashier\CashierServiceProvider::class)) {
            $this->components->warn('laravel/cashier missing; skip Cashier setup.');

            return;
        }

        $this->components->twoColumnDetail('Cashier', 'team billing via migrations');
    }

    protected function ensureSessionDriver(): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return;
        }

        $contents = File::get($envPath);

        if (preg_match('/^SESSION_DRIVER=/m', $contents)) {
            $this->components->twoColumnDetail('SESSION_DRIVER', 'already set');

            return;
        }

        File::put($envPath, rtrim($contents)."\nSESSION_DRIVER=database\n");
        $this->components->twoColumnDetail('SESSION_DRIVER', 'database (for session list UI)');
    }

    protected function ensureStorageLink(): void
    {
        $link = public_path('storage');

        if (File::exists($link)) {
            $this->components->twoColumnDetail('public/storage', 'linked');

            return;
        }

        $this->components->warn('Run php artisan storage:link for profile and team avatars.');
    }

    protected function ensureStripeEnv(): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            $this->components->warn('.env missing; add STRIPE_KEY / STRIPE_SECRET / STRIPE_WEBHOOK_SECRET manually.');

            return;
        }

        $contents = File::get($envPath);
        $stubs = [
            'STRIPE_KEY' => '',
            'STRIPE_SECRET' => '',
            'STRIPE_WEBHOOK_SECRET' => '',
            'CASHIER_CURRENCY' => 'usd',
            'ELECTRIK_ONBOARDING' => 'true',
            'ELECTRIK_REQUIRE_SUBSCRIPTION' => 'false',
        ];

        $added = [];
        foreach ($stubs as $key => $default) {
            if (preg_match('/^'.preg_quote($key, '/').'=/m', $contents)) {
                continue;
            }
            $contents = rtrim($contents)."\n{$key}={$default}";
            $added[] = $key;
        }

        if ($added === []) {
            $this->components->twoColumnDetail('Stripe .env', 'already present');

            return;
        }

        File::put($envPath, $contents."\n");
        $this->components->twoColumnDetail('Stripe .env', 'stubbed '.implode(', ', $added));
    }

    protected function printStripeWebhookHelp(): void
    {
        $url = url('/stripe/webhook');

        $this->newLine();
        $this->components->info('Stripe webhooks');
        $this->line('  Endpoint: '.$url);
        $this->line('  Local:    stripe listen --forward-to '.$url);
        $this->line('  Repair:   php artisan electrik:stripe:sync-subscriptions');
    }
}
