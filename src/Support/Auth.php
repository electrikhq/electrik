<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Auth
{
    /**
     * @return class-string<Authenticatable&Model>
     */
    public static function userModel(): string
    {
        /** @var class-string<Authenticatable&Model> $model */
        $model = config('auth.providers.users.model');

        return $model;
    }

    public static function home(): string
    {
        return (string) config('electrik.auth.home', '/dashboard');
    }

    public static function registrationEnabled(): bool
    {
        return (bool) config('electrik.auth.registration', true);
    }

    public static function emailVerificationEnabled(): bool
    {
        return (bool) config('electrik.auth.email_verification', true);
    }
}
