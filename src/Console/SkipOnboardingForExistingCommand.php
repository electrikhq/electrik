<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SkipOnboardingForExistingCommand extends Command
{
    protected $signature = 'electrik:onboarding:skip-existing';

    protected $description = 'Mark all users without onboarding completion as finished (use after upgrading)';

    public function handle(): int
    {
        $model = config('auth.providers.users.model');

        if (! is_string($model) || ! class_exists($model)) {
            $this->components->error('User model not configured.');

            return self::FAILURE;
        }

        $table = (new $model)->getTable();

        $count = DB::table($table)
            ->whereNull('onboarding_completed_at')
            ->update(['onboarding_completed_at' => now()]);

        $this->components->info("Marked {$count} existing user(s) as onboarding complete.");

        return self::SUCCESS;
    }
}
