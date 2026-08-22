<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'stripe_product_id',
        'name',
        'description',
    ];

    public function plans(): HasMany
    {
        return $this->hasMany(StripePlan::class, 'stripe_product_id');
    }
}
