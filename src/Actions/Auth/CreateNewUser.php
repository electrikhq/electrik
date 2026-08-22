<?php

namespace Electrik\Actions\Auth;

use Electrik\Support\UserModel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateNewUser
{
    /**
     * @param  array{name: string, email: string, password: string}  $data
     * @return Model&Authenticatable
     */
    public function execute(array $data): Authenticatable
    {
        $user = UserModel::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        return $user;
    }
}
