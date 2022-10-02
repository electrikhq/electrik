<?php

namespace Electrik\Http\Livewire\Modals\Billing;

use Electrik\Models\Team;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class PaymentMethod extends ModalComponent {

	use WireToast;

    public $plan;
    public $error;
    public $name;
    public $paymentmethod;
	public $paymentIntent;
	public $clientSecret;

	function rules() {
		return [
			'name' => 'required|string|max:255',
		];
	}

    public function mount(Team $team) {
		$this->team = $team;
		$paymentIntent = $this->team->createSetupIntent();
		$this->clientSecret = $paymentIntent->client_secret;
		// dd($this->clientSecret);
    }

    public static function modalMaxWidth(): string {
        return 'md';
    }


	public function refreshSetupIntent() {

		$paymentIntent = $this->team->createSetupIntent();
		$this->clientSecret = $paymentIntent->client_secret;

	}

    public function updateDefaultPaymentMethod($paymentMethod) {

		try { 
			$this->team->updateDefaultPaymentMethod($paymentMethod['payment_method']);
		} catch(\Exception $e) {
			$this->handleError(['message' => $e->getMessage()]);
		}
		
		$this->handleSucess();
		$this->closeModal();
    }

	public function cancel() {
		$this->closeModal();
	}
	public function handleError($error) {
		toast()->danger($error['message'])->push();
	}
	
	public function handleSucess() {
		toast()->success('Payment method updated successfully')->push();
    }


    public function render() {
        return view('electrik::livewire.modals.billing.payment-method', [
            'intent' => $this->team->createSetupIntent(),
        ]);
    }


}
