<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class UserModel
{
    /**
     * @return class-string<Model&Authenticatable>
     */
    public static function class(): string
    {
        /** @var class-string<Model&Authenticatable> $model */
        $model = config('auth.providers.users.model');

        return $model;
    }

    public static function query()
    {
        return static::class()::query();
    }
}
