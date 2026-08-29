<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Recovery\Recovery;

class TwoFactorAuth
{
    public static function enabled(?Authenticatable $user): bool
    {
        return $user
            && filled($user->two_factor_secret)
            && $user->two_factor_confirmed_at !== null;
    }

    public static function generateSecret(): string
    {
        return static::engine()->generateSecretKey();
    }

    /**
     * Inline QR (SVG/data URI) via pragmarx/google2fa-laravel + bacon/bacon-qr-code.
     */
    public static function qrInline(Authenticatable $user, string $secret): string
    {
        return static::engine()->getQRCodeInline(
            (string) config('electrik.name', 'Electrik'),
            (string) $user->email,
            $secret,
            180,
        );
    }

    public static function verify(Authenticatable $user, string $code): bool
    {
        if (! filled($user->two_factor_secret)) {
            return false;
        }

        $secret = Crypt::decryptString($user->two_factor_secret);

        return (bool) static::engine()->verifyKey($secret, $code);
    }

    public static function verifySecret(string $secret, string $code): bool
    {
        return (bool) static::engine()->verifyKey($secret, $code);
    }

    /**
     * @return list<string>
     */
    public static function generateRecoveryCodes(): array
    {
        return (new Recovery)
            ->setCount(8)
            ->setBlocks(2)
            ->setChars(4)
            ->toArray();
    }

    public static function encryptSecret(string $secret): string
    {
        return Crypt::encryptString($secret);
    }

    public static function encryptRecoveryCodes(array $codes): string
    {
        return Crypt::encryptString(json_encode(array_values($codes)));
    }

    /**
     * @return list<string>
     */
    public static function recoveryCodes(Authenticatable $user): array
    {
        if (! filled($user->two_factor_recovery_codes)) {
            return [];
        }

        $decoded = json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    protected static function engine(): mixed
    {
        return app('pragmarx.google2fa');
    }
}
