<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TeamWebhook extends Model
{
    protected $fillable = [
        'team_id',
        'url',
        'secret',
        'events',
        'enabled',
        'last_triggered_at',
        'failure_count',
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'enabled' => 'boolean',
            'last_triggered_at' => 'datetime',
            'failure_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TeamWebhook $webhook): void {
            if (blank($webhook->secret)) {
                $webhook->secret = Str::random(40);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(TeamWebhookDelivery::class);
    }

    public function listensFor(string $event): bool
    {
        $events = $this->events;

        if (! is_array($events) || $events === [] || in_array('*', $events, true)) {
            return true;
        }

        return in_array($event, $events, true);
    }
}
