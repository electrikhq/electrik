<?php

namespace Electrik\Support\Billing;

use Electrik\Models\Team;

interface BillingDriver
{
    /**
     * Driver key (currently stripe only).
     */
    public function name(): string;

    public function teamHasAccess(?Team $team): bool;

    public function subscriptionLabel(?Team $team): string;

    /**
     * Short hint for manage/portal UI (null when not applicable).
     */
    public function manageHint(): ?string;

    /**
     * External customer portal URL when the driver supports one.
     */
    public function portalUrl(?Team $team): ?string;

    /**
     * Whether required packages/env for this driver are present.
     */
    public function isConfigured(): bool;

    /**
     * User-facing message when the selected driver cannot run (missing package, etc.).
     */
    public function missingPackageMessage(): ?string;
}
