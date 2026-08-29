<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MagicLink
{
    public static function enabled(): bool
    {
        return filter_var(config('electrik.auth.magic_link.enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function expireMinutes(): int
    {
        return (int) config('electrik.auth.magic_link.expire_minutes', 15);
    }

    /**
     * Create a one-time nonce in cache and return a signed, expiring login URL.
     * The nonce is required in addition to the signature so a link can be
     * invalidated (single use) independent of the signature's own expiry.
     */
    public static function generate(Authenticatable $user): string
    {
        $nonce = Str::random(40);
        $expiresAt = now()->addMinutes(static::expireMinutes());

        Cache::put(static::cacheKey($user->getAuthIdentifier(), $nonce), true, $expiresAt);

        return URL::temporarySignedRoute('magic-link.login', $expiresAt, [
            'user' => $user->getAuthIdentifier(),
            'nonce' => $nonce,
        ]);
    }

    /**
     * Consume the nonce so the link cannot be replayed. Returns false if the
     * nonce is missing, unknown, or already used.
     */
    public static function consume(int|string $userId, ?string $nonce): bool
    {
        if (blank($nonce)) {
            return false;
        }

        return (bool) Cache::pull(static::cacheKey($userId, $nonce));
    }

    protected static function cacheKey(int|string $userId, string $nonce): string
    {
        return "electrik:magic-link:{$userId}:{$nonce}";
    }
}
