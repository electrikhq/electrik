<?php

namespace Electrik\Models;

use Laravel\Cashier\Billable;
use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    use Billable;

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }
}
