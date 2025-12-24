<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLocalDates;

class StripePlan extends Model
{
    use HasFactory, HasLocalDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stripe_product_id',
        'stripe_price_id',
        'name',
        'price',
        'currency',
        'interval',
        'interval_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'interval_count' => 'integer',
        ];
    }

    /**
     * Get the product that owns this plan.
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(StripeProduct::class, 'stripe_product_id');
    }

    /**
     * Check if this is a free plan.
     *
     * @return bool
     */
    public function isFree()
    {
        return $this->price === 0;
    }

    /**
     * Get the formatted price.
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->isFree()) {
            return 'FREE';
        }

        $amount = $this->price / 100; // Stripe stores prices in cents
        $currencySymbol = $this->getCurrencySymbol();

        return $currencySymbol . number_format($amount, 2);
    }

    /**
     * Get currency symbol.
     *
     * @return string
     */
    protected function getCurrencySymbol()
    {
        return match (strtoupper($this->currency)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            default => $this->currency . ' ',
        };
    }
}

