<?php

namespace Electrik\Console;

use Electrik\Support\EnsuresTeamRoles;
use Electrik\Support\Onboarding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class SeedDemoCommand extends Command
{
    protected $signature = 'electrik:seed-demo
                            {--email=demo@electrik.dev : Demo user email}
                            {--password=password : Demo user password}
                            {--force : Overwrite the demo user password if they already exist}';

    protected $description = 'Seed a demo user, team, and roles for local development';

    public function handle(EnsuresTeamRoles $ensures): int
    {
        $userModel = config('auth.providers.users.model');

        if (! is_string($userModel) || ! class_exists($userModel)) {
            $this->components->error('User model not configured.');

            return self::FAILURE;
        }

        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        /** @var \Illuminate\Contracts\Auth\Authenticatable|null $user */
        $user = $userModel::query()->where('email', $email)->first();

        if ($user && ! $this->option('force')) {
            $this->components->warn("Demo user {$email} already exists. Use --force to reset the password.");
        } else {
            if (! $user) {
                $user = $userModel::query()->create([
                    'name' => 'Demo User',
                    'email' => $email,
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->forceFill([
                    'password' => $password,
                    'email_verified_at' => now(),
                ])->save();
            }
        }

        if (! method_exists($user, 'createOwnedTeam')) {
            $this->components->error('User model is missing UserHasTeams.');

            return self::FAILURE;
        }

        $team = $user->teams()->first();

        if (! $team) {
            $team = $user->createOwnedTeam(['name' => 'Demo Team'], true);
        }

        $ensures->syncCatalog();
        $ensures->ensureForTeam($team);

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles(['owner']);
        }

        if (method_exists($user, 'switchTeam')) {
            $user->switchTeam($team);
        }

        Onboarding::markCompleted($user);

        $this->components->info('Demo workspace ready.');
        $this->line("  Email:    {$email}");
        $this->line("  Password: {$password}");
        $this->line('  Team:     '.$team->name);
        $this->line('  Login:    '.url('/login'));

        return self::SUCCESS;
    }
}
