<?php

namespace Electrik\Actions\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetUserPassword
{
    public function execute(Authenticatable $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));
    }
}
