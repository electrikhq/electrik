<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\HasLocalDates;

class Permission extends SpatiePermission
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

        // Scope permissions to current team
        static::addGlobalScope('team', function ($builder) {
            if (auth()->check() && auth()->user()->currentTeam) {
                $builder->where('team_id', auth()->user()->currentTeam->id);
            }
        });
    }

    /**
     * Get the permission display name.
     *
     * @param  string  $value
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return ucwords(str_replace(['-', '_'], ' ', $this->name));
    }
}

