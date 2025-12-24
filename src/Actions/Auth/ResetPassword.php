<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Events\User\UserPasswordReset;
use Illuminate\Support\Facades\Hash;

class ResetPassword
{
    /**
     * Execute the action.
     *
     * @param  User  $user
     * @param  string  $password
     * @return void
     */
    public function execute(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
        ]);

        // Fire event
        event(new UserPasswordReset($user));
    }
}
