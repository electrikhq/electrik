<?php

namespace App\Models;

use App\Traits\HasLocalDates;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends \Spatie\Permission\Models\Permission {

	/* cant use soft deletes. throws an exception with unique key */
	// use SoftDeletes;
	use HasLocalDates;

}