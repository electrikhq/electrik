<?php

namespace Electrik\Http\Controllers\Auth;

use Electrik\Support\TeamInviteContext;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $home = config('electrik.auth.home', '/dashboard');

        if ($request->user()->hasVerifiedEmail()) {
            return $this->afterVerified($home);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->afterVerified($home);
    }

    protected function afterVerified(string $home): RedirectResponse
    {
        if ($token = TeamInviteContext::peekToken()) {
            return redirect()->to(route('teams.invitations.accept', $token));
        }

        return redirect()->to($home.'?verified=1');
    }
}
