<?php

namespace App\Http\Livewire\Billing;

use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Subscription extends Component {

	use WireToast;

	public $subscriptions;
	public $team;

	public function mount() {
		$this->subscriptions = auth()->user()->currentTeam->subscriptions()->notCanceled()->get();
		$this->team = auth()->user()->currentTeam;
		// dd($this->subscription);
	}

	public function cancelSubscription($subscriptionId = null) {

		if($subscriptionId) {
			$subscription = $this->team->subscriptions()->where('stripe_id', $subscriptionId)->first();
			$subscription->cancelNow();
			toast()->success('Subscription cancelled successfully')->pushOnNextPage();
			return redirect()->route('billing.subscription');
		}

		if($this->team->subscribed(config('electrik.default_subscription_name'))) {
			$this->team->subscription(config('electrik.default_subscription_name'))->cancelNow();
			toast()->success('Subscription cancelled successfully')->pushOnNextPage();
			return redirect()->route('billing.subscription');
		} else {
			toast()->warning('No active subscription available for cancelling')->push();
		}
	}

    public function render() {

		if(count($this->subscriptions))
        	return view('livewire.billing.subscription');
		else 
			return view('livewire.billing.billing-not-found');


    }
}
