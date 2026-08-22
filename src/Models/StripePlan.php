<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripePlan extends Model
{
    protected $fillable = [
        'stripe_product_id',
        'stripe_price_id',
        'name',
        'price',
        'currency',
        'interval',
        'interval_count',
        'features',
        'max_seats',
        'seat_billing',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'interval_count' => 'integer',
            'features' => 'array',
            'max_seats' => 'integer',
            'seat_billing' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(StripeProduct::class, 'stripe_product_id');
    }

    public function isFree(): bool
    {
        return (int) $this->price === 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }

        $amount = $this->price / 100;
        $symbol = match (strtoupper($this->currency)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => strtoupper($this->currency).' ',
        };

        return $symbol.number_format($amount, 2);
    }
}
