<?php

namespace App\Events\Team;

use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamCreated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Team $team
    ) {
        //
    }
}

