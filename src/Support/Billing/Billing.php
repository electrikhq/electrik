<?php

namespace Electrik\Support\Billing;

use InvalidArgumentException;

class Billing
{
    public static function driver(?string $name = null): BillingDriver
    {
        $name = strtolower($name ?: (string) config('electrik.billing.driver', 'stripe'));

        return match ($name) {
            'stripe' => new StripeBillingDriver,
            default => throw new InvalidArgumentException(
                "Unsupported Electrik billing driver [{$name}]. Only stripe is supported. Set ELECTRIK_BILLING_DRIVER=stripe."
            ),
        };
    }

    public static function name(): string
    {
        return static::driver()->name();
    }

    public static function isStripe(): bool
    {
        return static::name() === 'stripe';
    }
}
