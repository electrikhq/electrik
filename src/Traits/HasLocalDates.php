<?php

namespace App\Traits;

use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * Custom trait built specifically for current application. 
 * 
 */
trait HasLocalDates {
    
	/**
	 * returns carbon
	 */
    protected function getCreatedAtAttribute($value) {
		$timezone = auth()->check() ? auth()->user()->timezone : (config('app.timezone'));
		return (empty($value) ? null : Carbon::parse(($value))->timezone($timezone));
    }
    
	protected function getUpdatedAtAttribute($value) {
		$timezone = auth()->check() ? auth()->user()->timezone : (config('app.timezone'));
		return (empty($value) ? null : Carbon::parse(($value))->timezone($timezone));
    }
	
	protected function getDeletedAtAttribute($value) {
		$timezone = auth()->check() ? auth()->user()->timezone : (config('app.timezone'));
		return (empty($value) ? null : Carbon::parse(($value))->timezone($timezone));
    }
	
	protected function getLastLoginAttribute($value) {
		$timezone = auth()->check() ? auth()->user()->timezone : (config('app.timezone'));
		return (empty($value) ? null : Carbon::parse(($value))->timezone($timezone));
    }

}