<?php

namespace Electrik\Tests\Unit;

use Electrik\Support\Onboarding;
use Electrik\Tests\TestCase;
use Illuminate\Foundation\Auth\User;

class OnboardingSupportTest extends TestCase
{
    public function test_onboarding_exempt_routes_include_billing_and_invites(): void
    {
        $this->assertTrue(Onboarding::isExemptRoute('billing.index'));
        $this->assertTrue(Onboarding::isExemptRoute('teams.invitations.accept'));
        $this->assertFalse(Onboarding::isExemptRoute('dashboard'));
    }

    public function test_onboarding_needs_incomplete_user(): void
    {
        $user = new User;
        $user->forceFill([
            'onboarding_completed_at' => null,
        ]);

        $this->assertTrue(Onboarding::needs($user));
        $this->assertFalse(Onboarding::completed($user));
    }
}
