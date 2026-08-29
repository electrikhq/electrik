<?php

namespace Electrik\Models;

use Electrik\Concerns\BelongsToTeam;
use Electrik\Support\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id',
        'project_id',
        'created_by',
        'assignee_id',
        'title',
        'description',
        'status',
        'priority',
        'due_on',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_on' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserModel::class(), 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(UserModel::class(), 'assignee_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'doing' => __('In progress'),
            'done' => __('Done'),
            'blocked' => __('Blocked'),
            default => __('To do'),
        };
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'high' => __('High'),
            'low' => __('Low'),
            default => __('Normal'),
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_on !== null
            && $this->status !== 'done'
            && $this->due_on->isPast();
    }
}
