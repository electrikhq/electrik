<?php

namespace Electrik\Models;

use Electrik\Traits\HasLocalDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model {

    use HasFactory;
	use HasLocalDates;

	protected $guarded = ['id'];

	public function addressable() {
		return $this->morphTo();
	}
	
}
