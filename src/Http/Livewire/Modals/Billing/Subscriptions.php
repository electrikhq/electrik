<?php

namespace App\Http\Livewire\Modals\Billing;

use App\Models\Address;
use App\Models\Team;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class Subscriptions  extends ModalComponent {

	use WireToast;

	public Team $team;
	public $clientSecret;

	public $currentPlan;
	public $selectedPlan;
	public $currentStep;
	public $currentSubscription;
	public $billingAddress;
	public $defaultPaymentMethod;
	public $showIndianStates;


	public function rules() {
		return [
			'name' => 'required|string|max:255',
			'billingAddress.name' => 'nullable',
			'billingAddress.address_1' => 'required',
			'billingAddress.address_2' => 'nullable',
			'billingAddress.city' => 'required',
			'billingAddress.state' => 'required',
			'billingAddress.country' => 'required',
			'billingAddress.pincode' => 'required',
		];

	}

	public function mount($data = []) {
		$this->team = Team::find($data['team']);
		$this->currentStep = 1;

		if($this->team->subscribed('sprrw')) {
		$this->currentSubscription = $this->team->subscription();
		}

		$this->defaultPaymentMethod = ($this->team->defaultPaymentMethod()) ? $this->team->defaultPaymentMethod()->toArray() : null;
		$this->billingAddress = $this->team->billingAddress()->exists() ? $this->team->billingAddress()->first() : new Address();
		$this->refreshSetupIntent();
	}

	public static function modalMaxWidth(): string {
		return '7xl';
	}

	public function stepOneSubmit($planId) {
		$this->selectedPlan = collect(config('plans.billables.team.plans'))->where('id', $planId)->first();
		$this->currentStep = 2;
	}

	public function refreshSetupIntent() {
		$paymentIntent = $this->team->createSetupIntent();
		$this->clientSecret = $paymentIntent->client_secret;
	}

	public function validateAndUpdateBillingAddress() {
		
		$validatedData = $this->validate([
			'billingAddress.name' => 'nullable',
			'billingAddress.address_1' => 'required',
			'billingAddress.city' => 'required',
			'billingAddress.state' => 'required',
			'billingAddress.country' => 'required',
			'billingAddress.pincode' => 'required',
		]);


		if($validatedData) {

			$this->billingAddress->save();
			
			if(!$this->team->billingAddress) {
				$this->team->billingAddress()->save($this->billingAddress);
			}
			
			return true;
		} else {
			return false;
		}
	}

	public function updatePaymentMethod($setupIntent) {
		
		$this->setupIntent = $setupIntent;

		try {
			$this->team->updateDefaultPaymentMethod($this->setupIntent['payment_method']);
		} catch(\Exception $e) {
			toast()->danger($e->getMessage());
		}
		
		if ($this->team->hasStripeId()) {
            $this->team->syncStripeCustomerDetails();
        } else {
			$stripeCustomer = $this->team->createOrGetStripeCustomer();
		}

	}

	public function handleError($error) {
		toast()->danger($error['message']);
	}
	
	public function handleSucess() {
		toast()->success('All Done.');
	}

	public function swapSubscription() {

		try {
			if(!$this->team->subscribed('sprrw')) {
				$this->team->newSubscription('sprrw', ($this->selectedPlan['prices'][strtolower(($this->team->billingAddress()->first()->country == 'IN' ) ? 'in': 'us')]['monthly']['id']))->create();
			} else {
				$this->team->subscription('sprrw')->noProrate()->swap($this->selectedPlan['prices'][strtolower(($this->team->billingAddress()->first()->country == 'IN' ) ? 'in': 'us')]['monthly']['id']);
			}

			$this->currentSubscription = $this->team->subscription('sprrw');
			
		} catch (IncompletePayment $exception) {
			return redirect()->route( 'cashier.payment', [$exception->payment->id, 'redirect' => route('billing.subscription')] ); 
		}

		toast()->success('Subcription updated successfully')->pushOnNextPage();

		return redirect()->route('billing.subscription');

		$this->closeModal();

	}


	public function render() {
        return view('livewire.modals.billing.subscriptions', [
            'intent' => $this->team->createSetupIntent(),
        ])->with('allIndianStates', getIndiaStateCodesArray())
		;
    }
}
