<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;

class Onboarding
{
    public static function enabled(): bool
    {
        return filter_var(config('electrik.onboarding.enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function completed(?Authenticatable $user): bool
    {
        if (! $user || ! static::enabled()) {
            return true;
        }

        return $user->onboarding_completed_at !== null;
    }

    public static function needs(?Authenticatable $user): bool
    {
        return static::enabled() && ! static::completed($user);
    }

    public static function markCompleted(Authenticatable $user): void
    {
        $user->forceFill(['onboarding_completed_at' => now()])->save();
    }

    public static function homePath(): string
    {
        $user = auth()->user();

        if (static::needs($user)) {
            return route('onboarding', absolute: false);
        }

        return config('electrik.auth.home', '/dashboard');
    }

    /**
     * Routes reachable before onboarding is complete.
     *
     * @return list<string>
     */
    public static function exemptRoutePatterns(): array
    {
        return config('electrik.onboarding.exempt_routes', [
            'onboarding',
            'billing.*',
            'teams.invitations.*',
            'verification.*',
            'logout',
        ]);
    }

    public static function isExemptRoute(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        foreach (static::exemptRoutePatterns() as $pattern) {
            if ($routeName === $pattern || str($routeName)->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
