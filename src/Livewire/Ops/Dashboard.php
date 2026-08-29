<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Models\StripePlan;
use Electrik\Models\Team;
use Electrik\Support\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Cashier\Subscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Operations')]
class Dashboard extends Component
{
    use AuthorizesOperatorAccess;

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function render()
    {
        return view('electrik::livewire.ops.dashboard', [
            'userCount' => UserModel::query()->count(),
            'teamCount' => Team::query()->count(),
            'pastDueCount' => $this->pastDueCount(),
            'failedJobCount' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0,
            'signups7' => $this->signupsSince(now()->subDays(7)),
            'signups30' => $this->signupsSince(now()->subDays(30)),
            'activeSubscriptions' => $this->activeSubscriptionCount(),
            'mrrCents' => $this->estimatedMrrCents(),
        ]);
    }

    protected function pastDueCount(): int
    {
        if (! class_exists(Subscription::class) || ! Schema::hasTable('subscriptions')) {
            return 0;
        }

        return $this->subscriptionQuery()
            ->whereIn('stripe_status', ['past_due', 'unpaid'])
            ->count();
    }

    protected function signupsSince(\DateTimeInterface $since): int
    {
        $model = UserModel::query()->getModel();
        $table = $model->getTable();

        if (! Schema::hasColumn($table, 'created_at')) {
            return 0;
        }

        return UserModel::query()->where('created_at', '>=', $since)->count();
    }

    protected function activeSubscriptionCount(): int
    {
        if (! class_exists(Subscription::class) || ! Schema::hasTable('subscriptions')) {
            return 0;
        }

        return $this->subscriptionQuery()
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->count();
    }

    /**
     * Rough MRR stub from local plan catalog × active subscription quantity.
     */
    protected function estimatedMrrCents(): int
    {
        if (! class_exists(Subscription::class) || ! Schema::hasTable('subscriptions') || ! Schema::hasTable('stripe_plans')) {
            return 0;
        }

        if (! Schema::hasTable('subscription_items') || ! Schema::hasColumn('subscription_items', 'stripe_price')) {
            return 0;
        }

        $typeColumn = $this->subscriptionTypeColumn();

        if (! $typeColumn) {
            return 0;
        }

        $rows = DB::table('subscription_items')
            ->join('subscriptions', 'subscription_items.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.'.$typeColumn, config('electrik.billing.subscription_name', 'electrik'))
            ->whereIn('subscriptions.stripe_status', ['active', 'trialing'])
            ->select('subscription_items.stripe_price', 'subscription_items.quantity')
            ->get();

        $plans = StripePlan::query()->get()->keyBy('stripe_price_id');
        $mrr = 0;

        foreach ($rows as $row) {
            $plan = $plans->get($row->stripe_price);

            if (! $plan || $plan->metered) {
                continue;
            }

            $qty = max(1, (int) $row->quantity);
            $monthly = match ($plan->interval) {
                'year' => (int) round($plan->price / 12),
                'week' => (int) round($plan->price * 52 / 12),
                'day' => (int) round($plan->price * 30),
                default => (int) $plan->price,
            };

            $mrr += $monthly * $qty;
        }

        return $mrr;
    }

    /**
     * Cashier 15+ uses `type`; older schemas used `name`.
     */
    protected function subscriptionTypeColumn(): ?string
    {
        if (Schema::hasColumn('subscriptions', 'type')) {
            return 'type';
        }

        if (Schema::hasColumn('subscriptions', 'name')) {
            return 'name';
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\Laravel\Cashier\Subscription>
     */
    protected function subscriptionQuery()
    {
        $query = Subscription::query();
        $column = $this->subscriptionTypeColumn();

        if ($column) {
            $query->where($column, config('electrik.billing.subscription_name', 'electrik'));
        }

        return $query;
    }
}
