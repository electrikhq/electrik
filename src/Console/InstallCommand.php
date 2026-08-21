<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'electrik:install
                            {--force : Overwrite published config if it exists}';

    protected $description = 'Install Electrik into a Laravel application (alpha stub)';

    public function handle(): int
    {
        $this->components->info('Installing Electrik '.$this->electrikVersion());

        $this->publishConfig();
        $this->ensureSlateCssImport();

        $this->newLine();
        $this->components->info('Electrik skeleton is ready.');
        $this->line('  Next: wire auth/teams/billing in later 5.x alphas.');
        $this->line('  UI: use <x-slate::*> — https://slate.electrik.dev');

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

    /**
     * Ensure app.css imports Slate tokens and scans package views.
     */
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

        $sourceLine = "@source '../../vendor/electrik/slate/resources/views';";
        if (! str_contains($contents, 'vendor/electrik/slate/resources/views')) {
            $contents = rtrim($contents)."\n\n".$sourceLine."\n";
            $changed = true;
        }

        $electrikSource = "@source '../../vendor/electrik/electrik/resources/views';";
        if (! str_contains($contents, 'vendor/electrik/electrik/resources/views')) {
            $contents = rtrim($contents)."\n".$electrikSource."\n";
            $changed = true;
        }

        if ($changed) {
            File::put($cssPath, $contents);
            $this->components->twoColumnDetail('resources/css/app.css', 'Slate + Electrik @source');
        } else {
            $this->components->twoColumnDetail('resources/css/app.css', 'already wired');
        }
    }
}
