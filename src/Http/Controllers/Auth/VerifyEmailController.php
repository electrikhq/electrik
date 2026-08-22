<?php

namespace Electrik\Http\Controllers\Auth;

use Electrik\Support\Onboarding;
use Electrik\Support\TeamInviteContext;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->afterVerified();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->afterVerified();
    }

    protected function afterVerified(): RedirectResponse
    {
        if ($token = TeamInviteContext::peekToken()) {
            return redirect()->to(route('teams.invitations.accept', $token));
        }

        $path = Onboarding::homePath();
        $suffix = str_contains($path, '?') ? '&verified=1' : '?verified=1';

        return redirect()->to($path.$suffix);
    }
}
