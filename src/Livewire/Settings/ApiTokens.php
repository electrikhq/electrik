<?php

namespace Electrik\Livewire\Settings;

use Carbon\CarbonInterface;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('API tokens')]
class ApiTokens extends Component
{
    public string $name = '';

    /**
     * Preset: never|7|30|90|365
     */
    public string $expiresIn = '90';

    /**
     * personal|team
     */
    public string $scope = 'personal';

    /** @var list<string> */
    public array $selectedAbilities = ['*'];

    public ?string $plainTextToken = null;

    public ?string $createdTokenName = null;

    public function mount(): void
    {
        abort_unless(class_exists(\Laravel\Sanctum\SanctumServiceProvider::class), 404);
        abort_unless(method_exists(auth()->user(), 'tokens'), 404);

        $default = (string) config('electrik.api_tokens.default_expiration_days', 90);
        $this->expiresIn = array_key_exists($default, $this->expirationOptions())
            ? $default
            : '90';

        $catalog = array_keys($this->abilityCatalog());
        $this->selectedAbilities = in_array('*', $catalog, true) ? ['*'] : array_slice($catalog, 0, 1);
    }

    public function updatedSelectedAbilities(): void
    {
        if (in_array('*', $this->selectedAbilities, true) && count($this->selectedAbilities) > 1) {
            $this->selectedAbilities = ['*'];
        }
    }

    public function createToken(): void
    {
        $team = auth()->user()->currentTeam;
        $catalog = array_keys($this->abilityCatalog());

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'expiresIn' => ['required', 'string', 'in:'.implode(',', array_keys($this->expirationOptions()))],
            'scope' => ['required', 'in:personal,team'],
            'selectedAbilities' => ['required', 'array', 'min:1'],
            'selectedAbilities.*' => ['string', 'in:'.implode(',', $catalog)],
        ]);

        $abilities = $this->normalizeAbilities($validated['selectedAbilities']);

        if ($validated['scope'] === 'team') {
            abort_unless($team instanceof Team, 422, __('Select a team before creating a team token.'));
            abort_unless(auth()->user()->belongsToTeam($team), 403);
            $this->authorizeTeamTokenManagement($team);
        }

        $newToken = auth()->user()->createToken(
            $validated['name'],
            $abilities,
            $this->expiresAtFromPreset($validated['expiresIn'])
        );

        if ($validated['scope'] === 'team' && $team) {
            $newToken->accessToken->forceFill(['team_id' => $team->getKey()])->save();
        }

        $this->plainTextToken = $newToken->plainTextToken;
        $this->createdTokenName = $validated['name'];
        $this->reset('name');
    }

    public function reissueToken(int $tokenId): void
    {
        $existing = auth()->user()->tokens()->whereKey($tokenId)->firstOrFail();

        $expiresAt = null;

        if ($existing->expires_at) {
            $lifetimeDays = max(1, (int) $existing->created_at->diffInDays($existing->expires_at));
            $expiresAt = now()->addDays($lifetimeDays);
        }

        $name = (string) $existing->name;
        $abilities = is_array($existing->abilities) && $existing->abilities !== []
            ? $existing->abilities
            : ['*'];
        $teamId = $existing->team_id;

        if ($teamId) {
            $team = Team::query()->find($teamId);
            abort_unless($team && auth()->user()->belongsToTeam($team), 403);
            $this->authorizeTeamTokenManagement($team);
        }

        $existing->delete();

        $newToken = auth()->user()->createToken($name, $abilities, $expiresAt);

        if ($teamId) {
            $newToken->accessToken->forceFill(['team_id' => $teamId])->save();
        }

        $this->plainTextToken = $newToken->plainTextToken;
        $this->createdTokenName = $name;
        $this->name = '';
    }

    public function deleteToken(int $tokenId): void
    {
        $existing = auth()->user()->tokens()->whereKey($tokenId)->firstOrFail();

        if ($existing->team_id) {
            $team = Team::query()->find($existing->team_id);
            if ($team) {
                $this->authorizeTeamTokenManagement($team);
            }
        }

        $existing->delete();

        if ($this->plainTextToken) {
            $this->plainTextToken = null;
            $this->createdTokenName = null;
        }

        session()->flash('status', __('Token revoked.'));
    }

    public function dismissPlainToken(): void
    {
        $this->plainTextToken = null;
        $this->createdTokenName = null;
    }

    /**
     * @return array<string, string>
     */
    public function expirationOptions(): array
    {
        return [
            'never' => __('No expiration'),
            '7' => __('7 days'),
            '30' => __('30 days'),
            '90' => __('90 days'),
            '365' => __('1 year'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function abilityCatalog(): array
    {
        $catalog = config('electrik.api_tokens.abilities', ['*' => 'Full access']);

        return is_array($catalog) ? $catalog : ['*' => 'Full access'];
    }

    /**
     * @param  list<string>  $abilities
     * @return list<string>
     */
    protected function normalizeAbilities(array $abilities): array
    {
        $abilities = array_values(array_unique(array_filter($abilities)));

        if (in_array('*', $abilities, true)) {
            return ['*'];
        }

        return $abilities !== [] ? $abilities : ['*'];
    }

    protected function authorizeTeamTokenManagement(Team $team): void
    {
        $user = auth()->user();

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        abort_unless(
            $user->isOwnerOfTeam($team) || $user->can('tokens.manage'),
            403,
            __('You do not have permission to manage team tokens.')
        );
    }

    protected function expiresAtFromPreset(string $preset): ?CarbonInterface
    {
        if ($preset === 'never') {
            return null;
        }

        $days = (int) $preset;

        return $days > 0 ? now()->addDays($days) : null;
    }

    public function render()
    {
        $user = auth()->user();
        $teams = method_exists($user, 'teams')
            ? $user->teams()->get()->keyBy('id')
            : collect();

        return view('electrik::livewire.settings.api-tokens', [
            'tokens' => $user->tokens()->orderByDesc('created_at')->get(),
            'expirationOptions' => $this->expirationOptions(),
            'abilityCatalog' => $this->abilityCatalog(),
            'currentTeam' => $user->currentTeam,
            'teams' => $teams,
        ]);
    }
}
