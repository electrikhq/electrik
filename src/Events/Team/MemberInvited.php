<?php

namespace App\Events\Team;

use App\Models\TeamInvite;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberInvited
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TeamInvite $invite
    ) {
        //
    }
}

