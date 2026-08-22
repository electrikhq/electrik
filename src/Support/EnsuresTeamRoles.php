<?php

namespace Electrik\Support;

use Electrik\Models\Permission;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Spatie\Permission\PermissionRegistrar;

class EnsuresTeamRoles
{
    public function syncCatalog(): void
    {
        $guard = config('auth.defaults.guard', 'web');
        $permissionModel = config('electrik.permissions.model', Permission::class);

        foreach (config('electrik.permissions.catalog', []) as $name => $meta) {
            $permission = $permissionModel::findOrCreate($name, $guard);
            $permission->fill([
                'display_name' => $meta['display_name'] ?? null,
                'category_name' => $meta['category'] ?? null,
                'category_description' => $meta['category_description'] ?? null,
            ])->save();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function ensureForTeam(Team|int $team): void
    {
        $teamId = $team instanceof Team ? $team->id : $team;
        $guard = config('auth.defaults.guard', 'web');
        $roleModel = config('electrik.permissions.role_model', Role::class);
        $permissionModel = config('electrik.permissions.model', Permission::class);

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        $matrix = config('electrik.permissions.role_permissions', []);
        $allNames = array_keys(config('electrik.permissions.catalog', []));

        foreach (config('electrik.teams.roles', ['owner', 'admin', 'member']) as $roleName) {
            $role = $roleModel::findOrCreateForTeam($roleName, $guard, $teamId);

            if (! $role->display_name) {
                $role->display_name = ucfirst($roleName);
                $role->save();
            }

            $wanted = $matrix[$roleName] ?? [];
            if (in_array('*', $wanted, true)) {
                $wanted = $allNames;
            }

            $ids = $permissionModel::query()->whereIn('name', $wanted)->pluck('id');
            $role->syncPermissions($ids);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Remove global (team_id NULL) roles when teams mode is enabled.
     */
    public function cleanupGlobalRoles(): int
    {
        if (! config('permission.teams', false)) {
            return 0;
        }

        $key = config('permission.column_names.team_foreign_key', 'team_id');
        $roleModel = config('electrik.permissions.role_model', Role::class);

        $deleted = 0;

        $roleModel::query()->whereNull($key)->each(function (Role $role) use (&$deleted) {
            $role->users()->detach();
            $role->permissions()->detach();
            $role->delete();
            $deleted++;
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $deleted;
    }
}
