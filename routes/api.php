<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Registered by ElectrikServiceProvider under Route::middleware('api')->prefix('api').
Route::middleware(['auth:sanctum', 'electrik.token-team', 'throttle:api'])->prefix('electrik')->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user();
        $team = $user?->currentTeam;
        $token = $user?->currentAccessToken();

        return [
            'id' => $user?->getAuthIdentifier(),
            'email' => $user?->email,
            'name' => $user?->name,
            'token' => $token ? [
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'expires_at' => $token->expires_at?->toIso8601String(),
            ] : null,
            'current_team' => $team ? [
                'id' => $team->id,
                'name' => $team->name,
                'archived' => method_exists($team, 'isArchived') ? $team->isArchived() : false,
            ] : null,
        ];
    })->middleware('electrik.ability:read,*')->name('api.electrik.me');

    Route::get('/team', function (Request $request) {
        $team = $request->user()?->currentTeam;

        abort_unless($team, 404);

        return [
            'id' => $team->id,
            'name' => $team->name,
            'brand_primary' => $team->brand_primary,
            'archived_at' => $team->archived_at?->toIso8601String(),
            'member_count' => $team->users()->count(),
        ];
    })->middleware('electrik.ability:read,*')->name('api.electrik.team');

    Route::get('/members', function (Request $request) {
        $team = $request->user()?->currentTeam;

        abort_unless($team, 404);

        return [
            'data' => $team->users()
                ->orderBy('name')
                ->get(['users.id', 'users.name', 'users.email'])
                ->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]),
        ];
    })->middleware('electrik.ability:read,*')->name('api.electrik.members');

    Route::post('/usage', function (Request $request) {
        $team = $request->user()?->currentTeam;

        abort_unless($team, 404);

        $validated = $request->validate([
            'stripe_price_id' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        app(\Electrik\Support\Billing\ReportUsage::class)->execute(
            $team,
            $validated['stripe_price_id'],
            $validated['quantity'],
        );

        return response()->json(['ok' => true]);
    })->middleware('electrik.ability:write,billing:write,*')->name('api.electrik.usage');
});
