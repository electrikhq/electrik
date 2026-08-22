<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamActivityLog extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        $model = config('auth.providers.users.model');

        return $this->belongsTo(is_string($model) ? $model : Team::class);
    }

    public function description(): string
    {
        $properties = array_merge([
            'name' => $this->user?->name,
            'email' => $this->properties['email'] ?? null,
            'role' => $this->properties['role'] ?? null,
        ], $this->properties ?? []);

        $key = 'electrik.activity.'.$this->action;
        $translated = __($key, $properties);

        return $translated !== $key ? $translated : str_replace('_', ' ', $this->action);
    }
}
