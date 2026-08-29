<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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

    /**
     * Operators can suspend users (Wave 4). Guarded with hasColumn so this is
     * safe to call before that migration has run.
     */
    public static function isSuspended(?Authenticatable $user): bool
    {
        if (! $user instanceof Model) {
            return false;
        }

        if (! Schema::hasColumn($user->getTable(), 'suspended_at')) {
            return false;
        }

        return $user->suspended_at !== null;
    }
}
