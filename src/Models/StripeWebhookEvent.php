<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeWebhookEvent extends Model
{
    protected $fillable = [
        'stripe_id',
        'type',
        'status',
        'customer_id',
        'team_id',
        'payload_summary',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_summary' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
