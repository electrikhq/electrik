<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Electrik
{
    /**
     * @return class-string<Model&Authenticatable>
     */
    public static function userModel(): string
    {
        /** @var class-string<Model&Authenticatable> $model */
        $model = config('auth.providers.users.model');

        return $model;
    }

    public static function homeRoute(): string
    {
        $home = config('electrik.auth.home', '/dashboard');

        if (is_string($home) && str_starts_with($home, '/')) {
            return $home;
        }

        return route($home);
    }

    public static function registrationEnabled(): bool
    {
        return (bool) config('electrik.auth.enable_registration', true);
    }

    public static function emailVerificationEnabled(): bool
    {
        return (bool) config('electrik.auth.enable_email_verification', true);
    }
}
