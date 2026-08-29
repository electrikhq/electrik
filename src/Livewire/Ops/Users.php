<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Support\UserModel;
use Illuminate\Support\Facades\Schema;
use Lab404\Impersonate\Services\ImpersonateManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Users')]
class Users extends Component
{
    use AuthorizesOperatorAccess;
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function suspend(int $userId): void
    {
        $this->authorizeOperator();
        abort_unless($this->suspendable(), 404);
        abort_if((int) auth()->id() === $userId, 422);

        $user = UserModel::query()->findOrFail($userId);
        $user->forceFill(['suspended_at' => now()])->save();

        session()->flash('status', __('User suspended.'));
    }

    public function unsuspend(int $userId): void
    {
        $this->authorizeOperator();
        abort_unless($this->suspendable(), 404);

        $user = UserModel::query()->findOrFail($userId);
        $user->forceFill(['suspended_at' => null])->save();

        session()->flash('status', __('User unsuspended.'));
    }

    public function impersonate(int $userId): void
    {
        $this->authorizeOperator();
        abort_unless($this->canImpersonate(), 404);
        abort_if((int) auth()->id() === $userId, 422);

        $user = UserModel::query()->findOrFail($userId);
        abort_unless(auth()->user()->impersonate($user), 422);

        $this->redirect(route('dashboard'), navigate: true);
    }

    protected function suspendable(): bool
    {
        return Schema::hasColumn(UserModel::query()->getModel()->getTable(), 'suspended_at');
    }

    protected function canImpersonate(): bool
    {
        return class_exists(ImpersonateManager::class)
            && method_exists(UserModel::class(), 'impersonate')
            && ! app(ImpersonateManager::class)->isImpersonating();
    }

    public function render()
    {
        $search = trim($this->search);

        $users = UserModel::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('electrik::livewire.ops.users', [
            'users' => $users,
            'suspendable' => $this->suspendable(),
            'canImpersonate' => $this->canImpersonate(),
        ]);
    }
}
