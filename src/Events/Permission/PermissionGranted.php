<?php

namespace App\Events\Permission;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermissionGranted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public Permission $permission
    ) {
        //
    }
}

