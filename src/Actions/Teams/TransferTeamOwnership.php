<?php

namespace Electrik\Actions\Teams;

use Electrik\Models\Team;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\PermissionRegistrar;

class TransferTeamOwnership
{
    /**
     * Transfer team ownership to another member and demote the current owner.
     */
    public function execute(
        Team $team,
        Authenticatable $currentOwner,
        Authenticatable $newOwner,
        string $demoteTo = 'admin',
    ): void {
        abort_unless(
            method_exists($currentOwner, 'isOwnerOfTeam') && $currentOwner->isOwnerOfTeam($team),
            403
        );

        abort_unless((int) $team->owner_id === (int) $currentOwner->getAuthIdentifier(), 403);
        abort_unless($team->users()->whereKey($newOwner->getAuthIdentifier())->exists(), 422);
        abort_if((int) $newOwner->getAuthIdentifier() === (int) $currentOwner->getAuthIdentifier(), 422);

        $demoteRole = in_array($demoteTo, ['admin', 'member'], true) ? $demoteTo : 'admin';

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        $team->update(['owner_id' => $newOwner->getAuthIdentifier()]);

        if (method_exists($currentOwner, 'syncRoles')) {
            $currentOwner->syncRoles([$demoteRole]);
        }

        if (method_exists($newOwner, 'syncRoles')) {
            $newOwner->syncRoles(['owner']);
        }
    }
}
