<?php

namespace App\Http\Livewire\Settings;


use Livewire\Component;

class TwoFactor extends Component {

	protected $google2fa;
	public $secretKey, $qrCode;

	function mount() {
		// Initialise the 2FA class
		$this->google2fa = app('pragmarx.google2fa')
		->setQrcodeService(
			new \PragmaRX\Google2FAQRCode\QRCode\Bacon(
				new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
			)
		);
		$this->secretKey = $this->google2fa->generateSecretKey();
		$this->qrCode =$this->google2fa->getQRCodeInline(
			config('app.name'),
			auth()->user()->email,
			$this->secretKey,
		);


	}

	public function submit() {
		toast()->info('All done')->push();
	}
    public function render() {
        return view('livewire.settings.two-factor');
    }
}
