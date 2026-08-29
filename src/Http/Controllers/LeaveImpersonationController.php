<?php

namespace Electrik\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Lab404\Impersonate\Services\ImpersonateManager;

class LeaveImpersonationController
{
    public function __invoke(ImpersonateManager $impersonate): RedirectResponse
    {
        if (! $impersonate->isImpersonating()) {
            return redirect()->route('dashboard');
        }

        auth()->user()?->leaveImpersonation();

        return redirect()->route('dashboard');
    }
}
