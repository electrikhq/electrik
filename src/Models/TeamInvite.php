<?php

namespace Electrik\Models;

use Mpociot\Teamwork\TeamInvite as TeamworkInvite;

class TeamInvite extends TeamworkInvite
{
    protected $fillable = [
        'user_id',
        'team_id',
        'type',
        'email',
        'role',
        'accept_token',
        'deny_token',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
