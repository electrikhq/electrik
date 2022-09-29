<?php

namespace Electrik\Http\Livewire\Onboarding;

use Electrik\Models\Address;
use GuzzleHttp\RedirectMiddleware;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Confirm extends Component {

	use WireToast;

	public $defaultPaymentMethod;
	public $team;
	public $currentSubscription;

	public Address $billingAddress;

	public $clientSecret;
	public $selectedPlan;

	protected $setupIntent;

	public $showIndianStates;

	public function rules() {
		return [
			'billingAddress.name' => 'nullable',
			'billingAddress.address_1' => 'required',
			// 'billingAddress.address_2' => 'nullable',
			'billingAddress.city' => 'required',
			'billingAddress.state' => 'required',
			'billingAddress.country' => 'required',
			'billingAddress.pincode' => 'required',
		];

	}

	public function mount() {

		if(!auth()->user()) {
			return redirect()->route('login');
		}

		$this->team = auth()->user()->currentTeam;

		if($this->team->subscribed('sprrw')) {
			$this->currentSubscription = $this->team->subscription('sprrw');
		}

		$this->defaultPaymentMethod = ($this->team->defaultPaymentMethod()) ? $this->team->defaultPaymentMethod()->toArray() : null;
		$this->billingAddress = $this->team->billingAddress()->exists() ? $this->team->billingAddress()->first() : new Address;

		$this->selectedPlan = collect(config('plans.billables.team.plans'))->where('slug', (auth()->user()->original_plan) ? auth()->user()->original_plan  : 'sprrw.core')->first();
		
		if($this->selectedPlan == null) $this->selectedPlan = collect(config('plans.billables.team.plans'))->where('slug', 'sprrw.core')->first();

		$this->showIndianStates = $this->billingAddress->country == 'IN';

		$this->refreshSetupIntent();

	}

	public function refreshSetupIntent() {
		$paymentIntent = $this->team->createSetupIntent();
		$this->clientSecret = $paymentIntent->client_secret;
	}

	public function updatedbillingAddressCountry($val) {
		if($val == 'IN' || $val == 'in') {
			$this->billingAddress->state = null;
			// dd('change country to india');
		}
		$this->showIndianStates = $this->billingAddress->country == 'IN';
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

	public function swapSubscription() {

		try {
			if(!$this->currentSubscription) {
				$this->team->newSubscription('sprrw', ($this->selectedPlan['prices'][strtolower(($this->team->billingAddress()->first()->country == 'IN' ) ? 'in': 'us')]['monthly']['id']))->create();
			} else {
				$this->team->subscription('sprrw')->skipTrial()->swapAndInvoice($this->selectedPlan['prices'][strtolower(($this->team->billingAddress()->first()->country == 'IN' ) ? 'in': 'us')]['monthly']['id']);
			}

			$this->currentSubscription = $this->team->subscription('sprrw');
			
		} catch (IncompletePayment $exception) {
			return redirect()->route( 'cashier.payment', [$exception->payment->id, 'redirect' => route('home')] ); 
		}

		toast()->success('Subcription updated successfully')->push();

		return redirect()->route('dashboard.index');

	}

	public function handleError($error) {
		toast()->danger($error['message'])->push();
	}
	
	public function handleSucess() {
		toast()->success('All Done.')->push();
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

	public function selectPlan($id) {
		$this->selectedPlan = collect(config('plans.billables.team.plans'))->where('id', $id)->first();
	}

    public function render() {
        return view('electrik::livewire.onboarding.confirm')
		->with('allIndianStates', getIndiaStateCodesArray())
		->layout('electrik::layouts.livewire.onboarding')
		;
    }
}
