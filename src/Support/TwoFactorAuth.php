<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

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
        return app(Google2FA::class)->generateSecretKey();
    }

    public static function qrUrl(Authenticatable $user, string $secret): string
    {
        $company = config('electrik.name', 'Electrik');

        return app(Google2FA::class)->getQRCodeUrl(
            $company,
            (string) $user->email,
            $secret
        );
    }

    public static function verify(Authenticatable $user, string $code): bool
    {
        if (! filled($user->two_factor_secret)) {
            return false;
        }

        $secret = Crypt::decryptString($user->two_factor_secret);

        return app(Google2FA::class)->verifyKey($secret, $code);
    }

    /**
     * @return list<string>
     */
    public static function generateRecoveryCodes(): array
    {
        return Collection::times(8, fn () => strtoupper(bin2hex(random_bytes(4))))
            ->map(fn (string $code) => substr($code, 0, 4).'-'.substr($code, 4))
            ->all();
    }

    public static function encryptSecret(string $secret): string
    {
        return Crypt::encryptString($secret);
    }

    public static function encryptRecoveryCodes(array $codes): string
    {
        return Crypt::encryptString(json_encode($codes));
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

        return is_array($decoded) ? $decoded : [];
    }
}
