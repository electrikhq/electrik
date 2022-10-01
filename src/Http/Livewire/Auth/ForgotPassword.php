<?php

namespace Electrik\Http\Livewire\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class ForgotPassword extends Component {
	
	use WireToast;

	public $email;

	public function mount() {
	}

	public function submit() {
		$this->validate([
			'email' => ['required', 'email'],
		]);

		// We will send the password reset link to this user. Once we have attempted
		// to send the link, we will examine the response then see the message we
		// need to show to the user. Finally, we'll send out a proper response.
		$status = Password::sendResetLink(
			['email' => $this->email]
		);

		if ($status == Password::RESET_LINK_SENT) {
			toast()->success(__($status))->push();

			return ;
        }

		toast()->danger(trans($status))->push();

	}
	
	public function render() {

        return view('electrik::livewire.auth.forgot-password')->layout('electrik::layouts.livewire.guest');
    }
}
