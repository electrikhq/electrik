<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasLocalDates
{
    /**
     * Get the created_at attribute with timezone conversion.
     *
     * @param  mixed  $value
     * @return Carbon|null
     */
    protected function getCreatedAtAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        $timezone = auth()->check() ? auth()->user()->timezone : config('app.timezone');
        
        return Carbon::parse($value)->timezone($timezone);
    }

    /**
     * Get the updated_at attribute with timezone conversion.
     *
     * @param  mixed  $value
     * @return Carbon|null
     */
    protected function getUpdatedAtAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        $timezone = auth()->check() ? auth()->user()->timezone : config('app.timezone');
        
        return Carbon::parse($value)->timezone($timezone);
    }

    /**
     * Get the deleted_at attribute with timezone conversion.
     *
     * @param  mixed  $value
     * @return Carbon|null
     */
    protected function getDeletedAtAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        $timezone = auth()->check() ? auth()->user()->timezone : config('app.timezone');
        
        return Carbon::parse($value)->timezone($timezone);
    }
}

