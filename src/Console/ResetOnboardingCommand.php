<?php

namespace Electrik\Console;

use Electrik\Support\Onboarding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetOnboardingCommand extends Command
{
    protected $signature = 'electrik:onboarding:reset
                            {email? : Reset a single user by email}
                            {--all : Reset onboarding for every user}';

    protected $description = 'Clear onboarding completion so user(s) see the wizard again';

    public function handle(): int
    {
        if (! Onboarding::enabled()) {
            $this->components->warn('Onboarding is disabled (ELECTRIK_ONBOARDING=false).');

            return self::SUCCESS;
        }

        $model = config('auth.providers.users.model');

        if (! is_string($model) || ! class_exists($model)) {
            $this->components->error('User model not configured.');

            return self::FAILURE;
        }

        $table = (new $model)->getTable();

        if ($this->option('all')) {
            $count = DB::table($table)->update(['onboarding_completed_at' => null]);
            $this->components->info("Reset onboarding for {$count} user(s).");

            return self::SUCCESS;
        }

        $email = $this->argument('email');

        if (! $email) {
            $this->components->error('Pass an email, or use --all.');

            return self::FAILURE;
        }

        $updated = DB::table($table)->where('email', $email)->update(['onboarding_completed_at' => null]);

        if ($updated === 0) {
            $this->components->error("No user found for {$email}.");

            return self::FAILURE;
        }

        $this->components->info("Onboarding reset for {$email}.");

        return self::SUCCESS;
    }
}
