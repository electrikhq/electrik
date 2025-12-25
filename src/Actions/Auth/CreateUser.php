<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Events\User\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser
{
    /**
     * Execute the action.
     *
     * @param  array  $data
     * @return User
     */
    public function execute(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'timezone' => $data['timezone'] ?? 'UTC',
            'original_plan' => $data['original_plan'] ?? null,
        ]);

        event(new UserRegistered($user));

        return $user;
    }
}
