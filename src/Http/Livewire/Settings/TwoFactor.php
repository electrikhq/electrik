<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;

class TwoFactor extends Component {

	protected $google2fa, $recovery, $currentUser;
	public $secretKey, $qrCode, $recoveryCodes;

	function mount() {

		$this->currentUser = auth()->user();

		if(!$this->currentUser->google_2fa_secret && !$this->currentUser->google_2fa_recovery_codes) {

			$this->google2fa = app('pragmarx.google2fa')
			->setQrcodeService(
				new \PragmaRX\Google2FAQRCode\QRCode\Bacon(
					new \BaconQrCode\Renderer\Image\SvgImageBackEnd(),
				)
			);
			$this->secretKey = $this->google2fa->generateSecretKey();
			$this->qrCode =$this->google2fa->getQRCodeInline(
				config('app.name'),
				$this->currentUser->email,
				$this->secretKey,
			);
	
			$this->recovery = new \PragmaRX\Recovery\Recovery();
			$this->recoveryCodes = $this->recovery->numeric()->setCount(4)->setBlocks(4)->setChars(3)->toArray();
			$this->currentUser->google2fa_secret = $this->secretKey;
			$this->currentUser->google2fa_recovery_codes = $this->recoveryCodes;
			$this->currentUser->save();
	
		} else {
			$this->recoveryCodes = $this->currentUser->google2fa_recovery_codes;
			$this->secretKey = $this->currentUser->google2fa_secret;
		}
		


	}

	public function submit() {

		
		toast()->info('All done')->push();
	}
    public function render() {
        return view('livewire.settings.two-factor');
    }
}
