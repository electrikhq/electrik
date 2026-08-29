<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamWebhookDelivery extends Model
{
    protected $fillable = [
        'team_webhook_id',
        'event',
        'status_code',
        'successful',
        'response_body',
    ];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'status_code' => 'integer',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(TeamWebhook::class, 'team_webhook_id');
    }
}
