<?php

namespace App\Models;

use App\Traits\HasLocalDates;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends \Spatie\Permission\Models\Role {

	// use SoftDeletes;
	use HasLocalDates;

	protected static $excluded = [
		// 1,
	];

	protected static function boot() {
        parent::boot();

        static::addGlobalScope('currentTeam', function ($builder) {

			if(auth()->user()) {
				$builder->where('roles.team_id', auth()->user()->currentTeam->id)->whereNotIn('id', self::$excluded);
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
        return ucwords($this->name);
    }
}
