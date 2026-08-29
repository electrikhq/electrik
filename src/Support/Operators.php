<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;

class Operators
{
    public static function emails(): array
    {
        $emails = config('electrik.operators.emails', []);

        return array_values(array_filter(array_map(
            static fn ($email) => strtolower(trim((string) $email)),
            is_array($emails) ? $emails : []
        )));
    }

    public static function check(?Authenticatable $user): bool
    {
        if (! $user || blank($user->email ?? null)) {
            return false;
        }

        $emails = static::emails();

        if ($emails === []) {
            return false;
        }

        return in_array(strtolower((string) $user->email), $emails, true);
    }
}
