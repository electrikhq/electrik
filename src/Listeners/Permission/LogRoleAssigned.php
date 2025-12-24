<?php

namespace App\Listeners\Permission;

use App\Events\Permission\RoleAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogRoleAssigned implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(RoleAssigned $event): void
    {
        Log::info('Role assigned', [
            'user_id' => $event->user->id,
            'role_id' => $event->role->id,
            'team_id' => $event->user->currentTeam?->id,
        ]);
    }
}

