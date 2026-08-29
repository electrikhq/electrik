<?php

namespace Electrik\Actions\Announcements;

use Electrik\Models\Announcement;
use Electrik\Models\Team;
use Electrik\Notifications\DatabaseNotification;
use Electrik\Support\Operators;
use Electrik\Support\UserModel;

class PublishAnnouncement
{
    /**
     * Fan out a database notification to every user matching the announcement's
     * audience. Returns the number of users notified.
     */
    public function execute(Announcement $announcement): int
    {
        $notified = 0;

        foreach ($this->recipients($announcement) as $user) {
            DatabaseNotification::send($user, $announcement->title, $announcement->body);
            $notified++;
        }

        return $notified;
    }

    /**
     * @return iterable<int, \Illuminate\Contracts\Auth\Authenticatable>
     */
    protected function recipients(Announcement $announcement): iterable
    {
        return match ($announcement->audience) {
            'operators' => $this->operatorUsers(),
            'plan' => $this->usersOnPlan($announcement->plan_price_id),
            default => UserModel::query()->cursor(),
        };
    }

    /**
     * @return iterable<int, \Illuminate\Contracts\Auth\Authenticatable>
     */
    protected function operatorUsers(): iterable
    {
        $emails = Operators::emails();

        if ($emails === []) {
            return [];
        }

        return UserModel::query()->whereIn('email', $emails)->cursor();
    }

    /**
     * Users belonging to any team with an active subscription on the given price.
     *
     * @return iterable<int, \Illuminate\Contracts\Auth\Authenticatable>
     */
    protected function usersOnPlan(?string $priceId): iterable
    {
        if (blank($priceId)) {
            return [];
        }

        $teamIds = Team::query()
            ->whereHas('subscriptions', function ($query) use ($priceId) {
                $query->where('stripe_price', $priceId)->active();
            })
            ->pluck('id');

        if ($teamIds->isEmpty()) {
            return [];
        }

        return UserModel::query()
            ->whereHas('teams', fn ($query) => $query->whereKey($teamIds->all()))
            ->cursor();
    }
}
