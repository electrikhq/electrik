<?php

namespace Electrik\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'category_name',
        'category_description',
    ];

    public function getDisplayNameAttribute(?string $value): string
    {
        return $value ?: ucwords(str_replace(['.', '-', '_'], ' ', (string) $this->name));
    }
}
