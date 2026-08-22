<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;
use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    use Billable;

    protected $fillable = [
        'name',
        'owner_id',
        'avatar_path',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TeamActivityLog::class)->latest();
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }
}
