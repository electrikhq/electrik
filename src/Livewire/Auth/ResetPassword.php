<?php

namespace App\Livewire\Auth;

use Livewire\Component;


use Illuminate\Auth\Events\PasswordReset;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Usernotnull\Toast\Concerns\WireToast;

class ResetPassword extends Component {
	
	use WireToast;

	public $email, $token, $password, $password_confirmation;

    public function rules() {
		return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
	}

	public function mount($token) {
		$this->token = $token;
		$this->email = request()->email;
		$this->password = null;
	}

	public function submit() {

        ($this->validate());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.

        $status = Password::reset(
			// $request->only('email', 'password', 'password_confirmation', 'token'),
			[
				'email' => $this->email,
				'password' => $this->password,
				'password_confirmation' => $this->password_confirmation,
				'token' => $this->token,
			],
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
			toast()->success(__($status))->pushOnNextPage();

			return redirect()->route('login');
        }

		// dd(__($status));
		toast()->danger(trans($status))->push();

    }

	public function render() {
        return view('livewire.auth.reset-password')->layout('layouts.livewire.guest');
    }

}
