<?php

namespace Electrik\Actions\Teams;

use Electrik\Actions\Billing\CancelSubscription;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Spatie\Permission\PermissionRegistrar;

class DeleteTeam
{
    public function __construct(protected CancelSubscription $cancelSubscription) {}

    /**
     * Permanently delete a team, its billing data, members, roles, and invites.
     */
    public function execute(Team $team, Authenticatable $actor): void
    {
        abort_unless(
            method_exists($actor, 'isOwnerOfTeam') && $actor->isOwnerOfTeam($team),
            403
        );

        abort_unless((int) $team->owner_id === (int) $actor->getAuthIdentifier(), 403);

        DB::transaction(function () use ($team) {
            $teamId = $team->id;

            $this->cancelSubscription->execute($team, immediately: true);

            if (method_exists($team, 'subscriptions')) {
                $team->subscriptions()->delete();
            }

            $team->invites()->delete();

            app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

            foreach ($team->users()->get() as $member) {
                if (method_exists($member, 'syncRoles')) {
                    $member->syncRoles([]);
                }

                if (method_exists($member, 'detachTeam')) {
                    $member->detachTeam($team);
                }
            }

            Role::query()->forTeam($teamId)->each(function (Role $role) {
                $role->users()->detach();
                $role->permissions()->detach();
                $role->delete();
            });

            if ($team->hasStripeId()) {
                try {
                    Cashier::stripe()->customers->delete($team->stripe_id);
                } catch (\Throwable) {
                    // Stripe customer may already be removed.
                }
            }

            $userModel = config('teamwork.user_model');
            if (is_string($userModel) && class_exists($userModel)) {
                $userModel::query()->where('current_team_id', $teamId)->update(['current_team_id' => null]);
            }

            $team->delete();
        });
    }
}
