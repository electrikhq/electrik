<?php

namespace Electrik\Concerns;

use Electrik\Support\Operators;

/**
 * Re-check operator access inside Livewire actions (route middleware does not
 * run again on component AJAX calls, only on the initial page load).
 */
trait AuthorizesOperatorAccess
{
    protected function authorizeOperator(): void
    {
        abort_unless(Operators::check(auth()->user()), 403);
    }
}
