<?php

namespace App\Models;

use App\Traits\HasLocalDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model {

    use HasFactory;
	use HasLocalDates;

	protected $guarded = ['id'];

    protected $casts = [

    ];

	public function configurable() {
		return $this->morphTo();
	}

}
