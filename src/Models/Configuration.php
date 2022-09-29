<?php

namespace Electrik\Models;

use Electrik\Traits\HasLocalDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model {

    use HasFactory;
	use HasLocalDates;

	protected $guarded = ['id'];

    protected $casts = [
        // 'value' => 'array',
    ];

	// public static function boot() {
	// 	parent::boot();
	// 	static::creating(function($bookmark) {
	// 		// dd(auth()->user()->currentTeam->id);
	// 		$bookmark->user_id = auth()->id();
	// 		$bookmark->team_id = auth()->user()->currentTeam->id;
	// 	});
	// }

	// public function newQuery($excludeDeleted = true) {
	// 	$teamId = (auth()->user()->currentTeam->id);
	// 	return parent::newQuery($excludeDeleted)
	// 	// ->where('user_id', auth()->id())
	// 	->where('team_id', $teamId)
	// 	;
	// }

	public function configurable() {
		return $this->morphTo();
	}

}
