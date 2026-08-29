<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class InstallCommand extends Command
{
    protected $signature = 'electrik:install
                            {--force : Overwrite published config and welcome view if they exist}
                            {--migrate : Run migrations after install}';

    protected $description = 'Install Electrik into a Laravel application';

    public function handle(): int
    {
        $this->publishConfig();
        $this->components->info('Installing Electrik '.$this->electrikVersion());

        $this->ensureSlateCssImport();
        $this->ensureWelcomeView();
        $this->ensureTeamwork();
        $this->ensurePermissionTeams();
        $this->ensureActivitylog();
        $this->ensureUserModel();
        $this->ensureSanctum();
        $this->ensureImpersonate();
        $this->ensurePersonalDataExport();
        $this->ensurePasskeys();
        $this->ensureAuthenticationLog();
        $this->ensureCashier();
        $this->ensureSessionDriver();
        $this->ensureLocaleMiddleware();
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
        $this->line('  Activity: spatie/laravel-activitylog (team scoped)');
        $this->line('  Auth log: rappasoft/laravel-authentication-log (Sessions)');
        $this->line('  2FA:      pragmarx/google2fa-laravel + bacon/bacon-qr-code');
        $this->line('  GDPR:     spatie/laravel-personal-data-export (profile export/delete)');
        $this->line('  Passkeys: laravel/passkeys (security + login)');

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

    protected function ensureWelcomeView(): void
    {
        $stub = dirname(__DIR__, 2).'/stubs/welcome.blade.php';
        $target = resource_path('views/welcome.blade.php');

        if (! File::exists($stub)) {
            $this->components->warn('Electrik welcome stub missing; skip welcome.blade.php.');

            return;
        }

        if (File::exists($target) && ! $this->option('force')) {
            $this->components->twoColumnDetail('resources/views/welcome.blade.php', 'exists');

            return;
        }

        File::ensureDirectoryExists(dirname($target));
        File::copy($stub, $target);
        $this->components->twoColumnDetail('resources/views/welcome.blade.php', 'published');
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

    protected function ensureActivitylog(): void
    {
        if (! class_exists(\Spatie\Activitylog\ActivitylogServiceProvider::class)) {
            $this->components->warn('spatie/laravel-activitylog missing; skip activity log setup.');

            return;
        }

        if (! File::exists(config_path('activitylog.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Spatie\\Activitylog\\ActivitylogServiceProvider',
                '--tag' => 'activitylog-config',
            ]);
            $this->components->twoColumnDetail('activitylog config', 'published');
        } else {
            $this->components->twoColumnDetail('activitylog config', 'exists');
        }

        config([
            'activitylog.activity_model' => \Electrik\Models\Activity::class,
        ]);
        $this->components->twoColumnDetail('activitylog.activity_model', 'Electrik\\Models\\Activity');
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

    protected function ensureImpersonate(): void
    {
        if (! class_exists(\Lab404\Impersonate\ImpersonateServiceProvider::class)) {
            $this->components->warn('lab404/laravel-impersonate missing; skip impersonation setup.');

            return;
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

        if (! str_contains($contents, 'Lab404\\Impersonate\\Models\\Impersonate')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Lab404\\Impersonate\\Models\\Impersonate;\n",
                $contents,
                1
            ) ?? $contents;
        }

        $hasTrait = (bool) preg_match('/^\s*use Impersonate;/m', $contents)
            || (bool) preg_match('/use [^;]*\bImpersonate\b[^;]*Notifiable/', $contents)
            || (bool) preg_match('/use [^;]*\bImpersonate\b[^;]*UserHasTeams/', $contents);

        if (! $hasTrait) {
            if (preg_match('/use HasApiTokens, HasFactory, HasRoles, Notifiable, UserHasTeams;/', $contents)) {
                $contents = preg_replace(
                    '/use HasApiTokens, HasFactory, HasRoles, Notifiable, UserHasTeams;/',
                    'use HasApiTokens, HasFactory, HasRoles, Impersonate, Notifiable, UserHasTeams;',
                    $contents,
                    1
                ) ?? $contents;
            } elseif (preg_match('/use HasFactory, HasRoles, Notifiable, UserHasTeams;/', $contents)) {
                $withSanctum = str_contains($contents, 'Laravel\\Sanctum\\HasApiTokens')
                    ? 'use HasApiTokens, HasFactory, HasRoles, Impersonate, Notifiable, UserHasTeams;'
                    : 'use HasFactory, HasRoles, Impersonate, Notifiable, UserHasTeams;';
                $contents = preg_replace(
                    '/use HasFactory, HasRoles, Notifiable, UserHasTeams;/',
                    $withSanctum,
                    $contents,
                    1
                ) ?? $contents;
            } else {
                $contents = preg_replace(
                    '/(class\s+User\s+extends\s+[^{]+\{)/',
                    "$1\n    use Impersonate;\n",
                    $contents,
                    1
                ) ?? $contents;
            }
        }

        if ($contents !== $original) {
            File::put($path, $contents);
            $this->components->twoColumnDetail('User model', 'lab404 Impersonate trait added');
        } else {
            $this->components->twoColumnDetail('User model', 'Impersonate already wired');
        }
    }

    protected function ensurePersonalDataExport(): void
    {
        if (! class_exists(\Spatie\PersonalDataExport\PersonalDataExportServiceProvider::class)) {
            $this->components->warn('spatie/laravel-personal-data-export missing; skip GDPR export setup.');

            return;
        }

        if (! File::exists(config_path('personal-data-export.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Spatie\\PersonalDataExport\\PersonalDataExportServiceProvider',
                '--tag' => 'personal-data-export-config',
            ]);
            $this->components->twoColumnDetail('personal-data-export config', 'published');
        } else {
            $this->components->twoColumnDetail('personal-data-export config', 'exists');
        }

        $this->ensurePersonalDataExportsDisk();
        $this->ensurePersonalDataExportAllowsEmailedDownloads();

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

        if (! str_contains($contents, 'Spatie\\PersonalDataExport\\ExportsPersonalData')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Spatie\\PersonalDataExport\\ExportsPersonalData;\n",
                $contents,
                1
            ) ?? $contents;
        }

        if (! str_contains($contents, 'Electrik\\Concerns\\ExportsElectrikPersonalData')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Electrik\\Concerns\\ExportsElectrikPersonalData;\n",
                $contents,
                1
            ) ?? $contents;
        }

        $contents = $this->ensureUserModelImplements($contents, 'ExportsPersonalData');
        $contents = $this->ensureUserModelUsesTrait($contents, 'ExportsElectrikPersonalData');

        if ($contents !== $original) {
            File::put($path, $contents);
            $this->components->twoColumnDetail('User model', 'ExportsPersonalData + ExportsElectrikPersonalData');
        } else {
            $this->components->twoColumnDetail('User model', 'personal data export already wired');
        }
    }

    protected function ensurePersonalDataExportAllowsEmailedDownloads(): void
    {
        $path = config_path('personal-data-export.php');

        if (! File::exists($path) || ! File::isWritable($path)) {
            return;
        }

        $contents = File::get($path);

        if (! preg_match("/['\"]authentication_required['\"]\\s*=>\\s*true/", $contents)) {
            $this->components->twoColumnDetail('personal-data-export.authentication_required', 'ok');

            return;
        }

        $updated = preg_replace(
            "/(['\"]authentication_required['\"]\\s*=>\\s*)true/",
            '${1}false',
            $contents,
            1
        );

        if (! is_string($updated) || $updated === $contents) {
            return;
        }

        File::put($path, $updated);
        $this->components->twoColumnDetail(
            'personal-data-export.authentication_required',
            'false (emailed download links)'
        );
    }

    protected function ensurePersonalDataExportsDisk(): void
    {
        $path = config_path('filesystems.php');

        if (! File::exists($path)) {
            $this->components->warn('config/filesystems.php missing; add personal-data-exports disk manually.');

            return;
        }

        $contents = File::get($path);

        if (str_contains($contents, "'personal-data-exports'") || str_contains($contents, '"personal-data-exports"')) {
            $this->components->twoColumnDetail('filesystems.disks.personal-data-exports', 'exists');

            return;
        }

        $disk = <<<'PHP'

        'personal-data-exports' => [
            'driver' => 'local',
            'root' => storage_path('app/personal-data-exports'),
        ],

PHP;

        if (preg_match("/('disks'\\s*=>\\s*\\[)/", $contents)) {
            $contents = preg_replace(
                "/('disks'\\s*=>\\s*\\[)/",
                "$1\n".$disk,
                $contents,
                1
            ) ?? $contents;
            File::put($path, $contents);
            $this->components->twoColumnDetail('filesystems.disks.personal-data-exports', 'added');
        } else {
            $this->components->warn('Could not locate disks array in filesystems.php; add personal-data-exports manually.');
        }
    }

    protected function ensurePasskeys(): void
    {
        if (! class_exists(\Laravel\Passkeys\PasskeysServiceProvider::class)) {
            $this->components->warn('laravel/passkeys missing; skip passkey setup.');

            return;
        }

        $published = collect(File::files(database_path('migrations')))
            ->contains(fn ($file) => str_contains($file->getFilename(), 'create_passkeys_table'));

        if (! $published) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'passkeys-migrations',
                '--force' => false,
            ]);
            $this->components->twoColumnDetail('passkeys migrations', 'published');
        } else {
            $this->components->twoColumnDetail('passkeys migrations', 'exists');
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

        if (! str_contains($contents, 'Laravel\\Passkeys\\Contracts\\PasskeyUser')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Laravel\\Passkeys\\Contracts\\PasskeyUser;\n",
                $contents,
                1
            ) ?? $contents;
        }

        if (! str_contains($contents, 'Laravel\\Passkeys\\PasskeyAuthenticatable')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Laravel\\Passkeys\\PasskeyAuthenticatable;\n",
                $contents,
                1
            ) ?? $contents;
        }

        $contents = $this->ensureUserModelImplements($contents, 'PasskeyUser');
        $contents = $this->ensureUserModelUsesTrait($contents, 'PasskeyAuthenticatable');

        if ($contents !== $original) {
            File::put($path, $contents);
            $this->components->twoColumnDetail('User model', 'PasskeyUser + PasskeyAuthenticatable');
        } else {
            $this->components->twoColumnDetail('User model', 'passkeys already wired');
        }
    }

    protected function ensureAuthenticationLog(): void
    {
        if (! class_exists(\Rappasoft\LaravelAuthenticationLog\LaravelAuthenticationLogServiceProvider::class)) {
            $this->components->warn('rappasoft/laravel-authentication-log missing; skip auth log setup.');

            return;
        }

        if (! File::exists(config_path('authentication-log.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Rappasoft\\LaravelAuthenticationLog\\LaravelAuthenticationLogServiceProvider',
                '--tag' => 'authentication-log-config',
            ]);
            $this->components->twoColumnDetail('authentication-log config', 'published');
        } else {
            $this->components->twoColumnDetail('authentication-log config', 'exists');
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

        if (! str_contains($contents, 'Rappasoft\\LaravelAuthenticationLog\\Traits\\AuthenticationLoggable')) {
            $contents = preg_replace(
                '/(namespace App\\\\Models;\\s+)/',
                "$1\nuse Rappasoft\\LaravelAuthenticationLog\\Traits\\AuthenticationLoggable;\n",
                $contents,
                1
            ) ?? $contents;
        }

        $contents = $this->ensureUserModelUsesTrait($contents, 'AuthenticationLoggable');

        if ($contents !== $original) {
            File::put($path, $contents);
            $this->components->twoColumnDetail('User model', 'AuthenticationLoggable');
        } else {
            $this->components->twoColumnDetail('User model', 'auth log already wired');
        }
    }

    /**
     * Add an interface to `class User extends X implements …` without breaking multiline braces.
     */
    protected function ensureUserModelImplements(string $contents, string $interface): string
    {
        if (preg_match('/class\s+User\s+extends\s+[^{]*\bimplements\b[^{]*\b'.preg_quote($interface, '/').'\b/s', $contents)) {
            return $contents;
        }

        if (preg_match('/class\s+User\s+extends\s+(\w+)\s+implements\s+([^\n{]+)/', $contents, $matches)) {
            $interfaces = array_values(array_unique(array_filter(array_map(
                static fn (string $part): string => trim($part, " \t,"),
                explode(',', $matches[2])
            ))));
            $interfaces[] = $interface;
            $interfaces = array_values(array_unique($interfaces));

            return preg_replace(
                '/class\s+User\s+extends\s+\w+\s+implements\s+[^\n{]+/',
                'class User extends '.$matches[1].' implements '.implode(', ', $interfaces),
                $contents,
                1
            ) ?? $contents;
        }

        return preg_replace(
            '/class\s+User\s+extends\s+(\w+)/',
            'class User extends $1 implements '.$interface,
            $contents,
            1
        ) ?? $contents;
    }

    /**
     * Ensure a trait appears in the primary `use …;` trait list inside the User class.
     * Never touches file-level `use Foo\Bar;` imports (those contain `\`).
     */
    protected function ensureUserModelUsesTrait(string $contents, string $trait): string
    {
        if (! preg_match('/class\s+User\s+extends\s+[^{]+\{/s', $contents, $classMatch, PREG_OFFSET_CAPTURE)) {
            return $contents;
        }

        $classBodyOffset = $classMatch[0][1] + strlen($classMatch[0][0]);
        $before = substr($contents, 0, $classBodyOffset);
        $after = substr($contents, $classBodyOffset);

        // Indented trait imports only (no namespaces).
        if (preg_match('/^\s{4}use\s+[^;\\\\]*\b'.preg_quote($trait, '/').'\b[^;\\\\]*;/m', $after)) {
            return $contents;
        }

        if (preg_match('/^\s{4}use\s+([^;\\\\]+);/m', $after, $classUse, PREG_OFFSET_CAPTURE)) {
            $traits = array_values(array_unique(array_filter(array_map(
                static fn (string $part): string => trim($part),
                explode(',', $classUse[1][0])
            ))));
            $traits[] = $trait;
            $traits = array_values(array_unique($traits));
            sort($traits);
            $replacement = '    use '.implode(', ', $traits).';';

            $lineStart = $classUse[0][1];
            $lineLength = strlen($classUse[0][0]);
            $after = substr($after, 0, $lineStart).$replacement.substr($after, $lineStart + $lineLength);

            return $before.$after;
        }

        return $before."\n    use {$trait};\n".$after;
    }

    protected function ensureCashier(): void
    {
        if (! class_exists(\Laravel\Cashier\CashierServiceProvider::class)) {
            $this->components->warn('laravel/cashier missing; skip Cashier setup.');

            return;
        }

        $this->components->twoColumnDetail('Cashier', 'team billing via migrations');
    }

    protected function ensureLocaleMiddleware(): void
    {
        $path = base_path('bootstrap/app.php');

        if (! File::exists($path) || ! File::isWritable($path)) {
            return;
        }

        $contents = File::get($path);

        if (str_contains($contents, 'Electrik\\Http\\Middleware\\SetLocale')) {
            $this->components->twoColumnDetail('SetLocale middleware', 'already wired');

            return;
        }

        if (! str_contains($contents, '->withMiddleware(function (Middleware $middleware)')) {
            $this->components->warn('Could not wire SetLocale into bootstrap/app.php automatically.');

            return;
        }

        $snippet = <<<'PHP'
        $middleware->web(append: [
            \Electrik\Http\Middleware\SetLocale::class,
        ]);
        $middleware->api(append: [
            \Electrik\Http\Middleware\BindTeamFromAccessToken::class,
        ]);
PHP;

        $updated = preg_replace(
            '/(->withMiddleware\(function\s*\(\s*Middleware\s+\$middleware\s*\)\s*(?::\s*void)?\s*\{\s*)/m',
            '$1'."\n".$snippet."\n",
            $contents,
            1
        );

        if (! is_string($updated) || $updated === $contents) {
            $this->components->warn('Could not insert SetLocale into bootstrap/app.php; append manually.');

            return;
        }

        File::put($path, $updated);
        $this->components->twoColumnDetail('SetLocale middleware', 'appended to web');
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

        if (File::exists($link) || is_link($link)) {
            $this->components->twoColumnDetail('public/storage', 'linked');

            return;
        }

        try {
            $this->callSilent('storage:link');
        } catch (\Throwable) {
            //
        }

        if (File::exists($link) || is_link($link)) {
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
            'ELECTRIK_BILLING_DRIVER' => 'stripe',
            'ELECTRIK_OPERATOR_EMAILS' => 'demo@example.com,demo@electrik.dev',
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
