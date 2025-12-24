<?php

namespace App\Events\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamSwitched
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public Team $team
    ) {
        //
    }
}

