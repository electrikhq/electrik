<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLocalDates;

class StripeProduct extends Model
{
    use HasFactory, HasLocalDates, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stripe_product_id',
        'name',
        'description',
    ];

    /**
     * Get the plans (prices) for this product.
     *
     * @return HasMany
     */
    public function plans()
    {
        return $this->hasMany(StripePlan::class, 'stripe_product_id');
    }
}

