<?php

namespace Electrik\Livewire\Teams;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Electrik\Models\TeamWebhook;
use Electrik\Support\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Webhooks')]
class Webhooks extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public string $url = '';

    public string $eventsText = '*';

    public function mount(Team $team): void
    {
        $this->authorizeTeamPermission($team, 'teams.manage');
        $this->team = $team;
    }

    public function create(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $validated = $this->validate([
            'url' => ['required', 'url', 'max:2048'],
            'eventsText' => ['required', 'string', 'max:500'],
        ]);

        $events = collect(preg_split('/[\s,]+/', $validated['eventsText']) ?: [])
            ->map(fn ($e) => trim((string) $e))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $webhook = $this->team->webhooks()->create([
            'url' => $validated['url'],
            'events' => $events === [] ? ['*'] : $events,
            'enabled' => true,
        ]);

        ActivityLogger::log($this->team, 'team.webhook_created', auth()->user(), $webhook);

        $this->reset('url', 'eventsText');
        $this->eventsText = '*';

        session()->flash('status', __('Webhook endpoint created. Secret: :secret', ['secret' => $webhook->secret]));
    }

    public function toggle(int $webhookId): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $webhook = $this->team->webhooks()->whereKey($webhookId)->firstOrFail();
        $webhook->update(['enabled' => ! $webhook->enabled]);

        session()->flash('status', $webhook->enabled ? __('Webhook enabled.') : __('Webhook disabled.'));
    }

    public function delete(int $webhookId): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $webhook = $this->team->webhooks()->whereKey($webhookId)->firstOrFail();
        ActivityLogger::log($this->team, 'team.webhook_deleted', auth()->user(), $webhook);
        $webhook->delete();

        session()->flash('status', __('Webhook deleted.'));
    }

    public function render()
    {
        $this->bindTeamContext($this->team);

        return view('electrik::livewire.teams.webhooks', [
            'webhooks' => $this->team->webhooks()->latest()->get(),
        ]);
    }
}
