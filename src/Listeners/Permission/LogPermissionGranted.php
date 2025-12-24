<?php

namespace App\Listeners\Permission;

use App\Events\Permission\PermissionGranted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogPermissionGranted implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PermissionGranted $event): void
    {
        Log::info('Permission granted', [
            'user_id' => $event->user->id,
            'permission_id' => $event->permission->id,
            'team_id' => $event->user->currentTeam?->id,
        ]);
    }
}

