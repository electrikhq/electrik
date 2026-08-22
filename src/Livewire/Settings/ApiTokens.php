<?php

namespace Electrik\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('API tokens')]
class ApiTokens extends Component
{
    public string $name = '';

    public ?string $plainTextToken = null;

    public function mount(): void
    {
        abort_unless(class_exists(\Laravel\Sanctum\SanctumServiceProvider::class), 404);
        abort_unless(method_exists(auth()->user(), 'tokens'), 404);
    }

    public function createToken(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = auth()->user()->createToken($validated['name']);

        $this->plainTextToken = $token->plainTextToken;
        $this->reset('name');

        session()->flash('status', __('Token created. Copy it now — it will not be shown again.'));
    }

    public function deleteToken(int $tokenId): void
    {
        auth()->user()->tokens()->whereKey($tokenId)->delete();
        session()->flash('status', __('Token revoked.'));
    }

    public function render()
    {
        return view('electrik::livewire.settings.api-tokens', [
            'tokens' => auth()->user()->tokens()->orderByDesc('created_at')->get(),
        ]);
    }
}
