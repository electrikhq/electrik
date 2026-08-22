<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'team_id',
        'display_name',
    ];

    public function getDisplayNameAttribute(?string $value): string
    {
        return $value ?: ucwords(str_replace(['-', '_'], ' ', (string) $this->name));
    }

    public function isSystem(): bool
    {
        return in_array($this->name, config('electrik.teams.roles', ['owner', 'admin', 'member']), true);
    }

    /**
     * Scope roles to a team (Spatie does not auto-filter Role::query() by team).
     */
    public function scopeForTeam(Builder $query, int|string|null $teamId = null): Builder
    {
        $teamId ??= app(PermissionRegistrar::class)->getPermissionsTeamId();
        $key = config('permission.column_names.team_foreign_key', 'team_id');

        return $query->where($key, $teamId);
    }

    /**
     * Find or create a role scoped to a team (avoids Spatie findOrCreate matching team_id NULL rows).
     */
    public static function findOrCreateForTeam(string $name, string $guard, int $teamId): self
    {
        $key = config('permission.column_names.team_foreign_key', 'team_id');

        $role = static::query()
            ->where('name', $name)
            ->where('guard_name', $guard)
            ->where($key, $teamId)
            ->first();

        if ($role) {
            return $role;
        }

        return static::create([
            'name' => $name,
            'guard_name' => $guard,
            $key => $teamId,
        ]);
    }
}
