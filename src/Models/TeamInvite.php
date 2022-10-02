<?php

namespace Electrik\Models;

use Electrik\Traits\HasLocalDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamInvite extends \Mpociot\Teamwork\TeamInvite {

    use HasFactory;
    use HasLocalDates;

	protected $guarded = [
		'id'
	];
	
	protected static function boot() {
        parent::boot();

        static::addGlobalScope('currentTeam', function ($builder) {

			if(auth()->user()) {
				$builder->where('team_invites.team_id', auth()->user()->currentTeam->id);//->whereNotIn('id', self::$excluded);
			}

        });
    }

	public function role() {
		return $this->belongsTo(Role::class);
	}
	
	public function user() {
		return $this->belongsTo(User::class);
	}
	
	public function team() {
		return $this->belongsTo(Team::class);
	}

}
