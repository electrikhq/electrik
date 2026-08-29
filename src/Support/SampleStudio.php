<?php

namespace Electrik\Support;

use Illuminate\Support\Facades\Schema;

class SampleStudio
{
    public static function enabled(): bool
    {
        return (bool) config('electrik.sample.projects', true)
            && Schema::hasTable('projects')
            && Schema::hasTable('clients')
            && Schema::hasTable('tasks');
    }
}
