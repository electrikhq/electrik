<?php

namespace Electrik\Models;

use Electrik\Concerns\BelongsToTeam;
use Electrik\Support\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id',
        'client_id',
        'created_by',
        'name',
        'description',
        'status',
        'due_on',
    ];

    protected function casts(): array
    {
        return [
            'due_on' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserModel::class(), 'created_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paused' => __('Paused'),
            'done' => __('Done'),
            default => __('Active'),
        };
    }

    public function openTasksCount(): int
    {
        return $this->tasks()->where('status', '!=', 'done')->count();
    }
}
