<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'brand_logo_path',
        'brand_primary',
        'archived_at',
        'allowed_ips',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'archived_at' => 'datetime',
            'allowed_ips' => 'array',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(TeamWebhook::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function brandLogoUrl(): ?string
    {
        if (! $this->brand_logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->brand_logo_path);
    }

    public function brandPrimary(): ?string
    {
        return $this->brand_primary ?: null;
    }

    /**
     * @return list<string>
     */
    public function allowedIpList(): array
    {
        $ips = $this->allowed_ips;

        if (! is_array($ips)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($ip) => trim((string) $ip),
            $ips
        )));
    }

    public function allowsIp(?string $ip): bool
    {
        $allowed = $this->allowedIpList();

        if ($allowed === [] || blank($ip)) {
            return true;
        }

        return in_array($ip, $allowed, true);
    }
}
