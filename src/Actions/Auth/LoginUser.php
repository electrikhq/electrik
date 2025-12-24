<?php

namespace App\Actions\Auth;

use App\Events\User\UserLoggedIn;
use Illuminate\Support\Facades\Auth;

class LoginUser
{
    /**
     * Execute the action.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function execute(array $credentials): bool
    {
        $success = Auth::attempt($credentials);

        if ($success) {
            // Fire event
            event(new UserLoggedIn(Auth::user()));
        }

        return $success;
    }
}
