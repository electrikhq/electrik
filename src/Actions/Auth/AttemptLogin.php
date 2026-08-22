<?php

namespace Electrik\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class AttemptLogin
{
    /**
     * @param  array{email: string, password: string}  $credentials
     */
    public function execute(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember);
    }
}
