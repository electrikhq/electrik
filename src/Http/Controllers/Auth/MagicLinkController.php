<?php

namespace Electrik\Http\Controllers\Auth;

use Electrik\Notifications\MagicLinkNotification;
use Electrik\Support\Auth as ElectrikAuth;
use Electrik\Support\MagicLink;
use Electrik\Support\Onboarding;
use Electrik\Support\UserModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class MagicLinkController
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(MagicLink::enabled(), 404);

        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $user = UserModel::query()->where('email', $validated['email'])->first();

        if ($user && ! ElectrikAuth::isSuspended($user)) {
            Notification::send($user, new MagicLinkNotification(MagicLink::generate($user)));
        }

        // Same message whether or not the email matched, so we don't leak account existence.
        return back()->with('status', __('If that email has an account, we sent a sign-in link.'));
    }

    public function login(Request $request, int|string $user): RedirectResponse
    {
        abort_unless(MagicLink::enabled(), 404);

        $userModel = UserModel::query()->find($user);

        if (! $userModel || ! MagicLink::consume($userModel->getAuthIdentifier(), $request->query('nonce'))) {
            return redirect()->route('login')->with('error', __('This sign-in link is invalid or has expired.'));
        }

        if (ElectrikAuth::isSuspended($userModel)) {
            return redirect()->route('login')->with('error', __('This account has been suspended.'));
        }

        Auth::login($userModel);
        $request->session()->regenerate();

        return redirect()->to(Onboarding::homePath());
    }
}
