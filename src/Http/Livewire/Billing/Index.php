<?php

namespace App\Http\Livewire\Billing;

use App\Models\Address;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Index extends Component {

	use WireToast;

	public $team;

	public $plans;

	public $in_gst;

	public Address $billingAddress;

	public $showIndianStates;
	
	protected function rules() {
		return [
			'billingAddress.name' => 'required',
			'billingAddress.address_1' => 'required',
			'billingAddress.address_2' => 'nullable',
			'billingAddress.city' => 'required',
			'billingAddress.state' => 'required',
			'billingAddress.country' => 'required',
			'billingAddress.pincode' => 'required',
		];
	}

	public function mount() {
		$this->team = auth()->user()->currentTeam;

		if($this->team->billingAddress()->exists()) {
			$this->billingAddress = $this->team->billingAddress()->first();
			if($this->billingAddress->tax_ids)
				$this->in_gst = json_decode($this->billingAddress->tax_ids, true)['value'];

		} else {
			$this->billingAddress = new Address;
		}

		$this->plans = config('plans.billables');

		$this->showIndianStates = $this->billingAddress->country == 'IN';

	}

	public function updateTaxId() {

        $validatedData = $this->validate([
            'in_gst' => ['required', 'regex:/[0-9]{2}[A-Z]{3}[ABCFGHLJPTF]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}/', function($attributr, $value, $fail) {
				// dd($value, gstToStateCode($value), $this->team->billingAddress()->exists());
				if($this->team->billingAddress()->exists() && $this->team->billingAddress()->first()->state != '' && $this->team->billingAddress()->first()->state != gstToStateCode($value)) {
					$fail('Provided GST does not match the billing address state. Please check GSTIN.');
				}
				
				if($this->team->billingAddress()->exists() && $this->team->billingAddress()->first()->country != 'IN') {
					$fail('Existing Billing Address does not belong to India. GST can only be provided billing addresses falling in India.');
				}
				
			}],
        ]);

		$taxId = [];

		try {

			if($this->team->billingAddress()->exists()) {
				
				if($this->team->billingAddress->tax_ids) {
					$this->team->deleteTaxId(json_decode($this->team->billingAddress->tax_ids, true)['id']);

				}
			}


			$taxId = $this->team->createTaxId('in_gst', $validatedData['in_gst']);
			$taxId = ($taxId->jsonSerialize());
		

		} catch(\Exception $e) {
			toast()->danger($e->getMessage())->push();
			return;
		}

		$billingAddress = $this->team->billingAddress()->firstOrNew();
		
		$billingAddress->tax_ids = json_encode($taxId);

		if(!$billingAddress->exists) {
			$billingAddress->country = 'IN';
			$billingAddress->state = gstToStateCode($validatedData['in_gst']);
		}

		$billingAddress->save();

		if ($this->team->hasStripeId()) {
            $this->team->syncStripeCustomerDetails();
        }

		toast()->success('Tax ID has been updated successfully')->push();

		return redirect()->route('billing.index');


	}


	public function updatedbillingAddressCountry($val) {
		if($val == 'IN' || $val == 'in') {
			$this->billingAddress->state = null;
			// dd('change country to india');
		}
		$this->showIndianStates = $this->billingAddress->country == 'IN';
	}
	
	public function updateBillingAddress() {
		
		$this->validate();

		$this->billingAddress->save();
		
		if(!$this->team->billingAddress()->exists()) {
			$this->team->billingAddress()->save($this->billingAddress);
		}

		if ($this->team->hasStripeId()) {
            $this->team->syncStripeCustomerDetails();
        }

		toast()->success('Billing details updated successfully')->pushOnNextPage();

		return redirect()->route('billing.index');
	}

    public function render() {
		// if(!$this->team->subscriptions) {
			return view('livewire.billing.index')
			->with('allIndianStates', getIndiaStateCodesArray())
			;
		// }
    }
}
