<?php

namespace Electrik\Models;

use Electrik\Concerns\BelongsToTeam;
use Electrik\Support\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id',
        'created_by',
        'name',
        'email',
        'company',
        'status',
        'notes',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserModel::class(), 'created_by');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paused' => __('Paused'),
            'archived' => __('Archived'),
            default => __('Active'),
        };
    }
}
