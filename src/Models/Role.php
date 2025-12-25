<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\HasLocalDates;

class Role extends SpatieRole
{
    use HasLocalDates;

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Scope roles to current team
        static::addGlobalScope('team', function ($builder) {
            if (auth()->check() && auth()->user()->currentTeam) {
                $builder->where('roles.team_id', auth()->user()->currentTeam->id);
            }
        });
    }

    /**
     * Get the role display name.
     *
     * @param  string  $value
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return ucwords(str_replace(['-', '_'], ' ', $this->name));
    }
}

