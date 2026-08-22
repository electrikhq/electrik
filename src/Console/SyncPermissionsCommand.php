<?php

namespace Electrik\Console;

use Electrik\Models\Team;
use Electrik\Support\EnsuresTeamRoles;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'electrik:permissions:sync
                            {--teams : Also refresh system roles for every team}
                            {--cleanup-global-roles : Delete roles with null team_id when teams mode is enabled}';

    protected $description = 'Sync Electrik permission catalog (and optional team system roles)';

    public function handle(EnsuresTeamRoles $ensures): int
    {
        $this->components->info('Syncing permission catalog…');
        $ensures->syncCatalog();
        $this->components->twoColumnDetail('permissions', (string) count(config('electrik.permissions.catalog', [])));

        if ($this->option('cleanup-global-roles')) {
            $removed = $ensures->cleanupGlobalRoles();
            $this->components->twoColumnDetail('removed global roles', (string) $removed);
        }

        if ($this->option('teams')) {
            $this->components->info('Refreshing system roles per team…');
            Team::query()->orderBy('id')->each(function (Team $team) use ($ensures) {
                $ensures->ensureForTeam($team);
                $this->line('  · '.$team->name);
            });
        }

        $this->components->info('Done.');

        return self::SUCCESS;
    }
}
