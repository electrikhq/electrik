<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLocalDates;

class TeamInvite extends Model
{
    use HasFactory, HasLocalDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'email',
        'token',
        'role_id',
        'user_id',
    ];

    /**
     * Get the team that owns the invite.
     *
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the role for the invite.
     *
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who was invited (if they exist).
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the invite is still valid.
     *
     * @return bool
     */
    public function isValid()
    {
        // TODO: Add expiration logic
        return true;
    }
}

