<?php

namespace Electrik\Models;

use Electrik\Support\BillingStatus;
use Electrik\Support\Operators;
use Electrik\Support\UserModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'body',
        'audience',
        'plan_price_id',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserModel::class(), 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function isActive(): bool
    {
        $now = now();

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function audienceLabel(): string
    {
        return match ($this->audience) {
            'operators' => __('Operators'),
            'plan' => __('Users on a plan'),
            default => __('Everyone'),
        };
    }

    /**
     * Does this announcement's audience rule match the given user?
     */
    public function matchesUser(Authenticatable $user): bool
    {
        return match ($this->audience) {
            'operators' => Operators::check($user),
            'plan' => $this->matchesPlan($user),
            default => true,
        };
    }

    protected function matchesPlan(Authenticatable $user): bool
    {
        if (blank($this->plan_price_id)) {
            return false;
        }

        $team = $user->currentTeam ?? null;

        if (! $team) {
            return false;
        }

        $subscription = BillingStatus::subscriptionFor($team);
        $priceId = $subscription?->stripe_price ?? $subscription?->items()->first()?->stripe_price;

        return $priceId && $priceId === $this->plan_price_id;
    }

    /**
     * The first active announcement matching this user, if any.
     */
    public static function activeFor(?Authenticatable $user): ?self
    {
        if (! $user) {
            return null;
        }

        return static::query()
            ->active()
            ->latest()
            ->get()
            ->first(fn (self $announcement) => $announcement->matchesUser($user));
    }
}
