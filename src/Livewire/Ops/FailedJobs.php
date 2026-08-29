<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Failed jobs')]
class FailedJobs extends Component
{
    use AuthorizesOperatorAccess;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function retry(string $uuid): void
    {
        $this->authorizeOperator();
        abort_unless($this->tableExists(), 404);

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        session()->flash('status', __('Job queued for retry.'));
    }

    public function retryAll(): void
    {
        $this->authorizeOperator();
        abort_unless($this->tableExists(), 404);

        Artisan::call('queue:retry', ['id' => ['all']]);

        session()->flash('status', __('All failed jobs queued for retry.'));
    }

    public function forget(string $uuid): void
    {
        $this->authorizeOperator();
        abort_unless($this->tableExists(), 404);

        Artisan::call('queue:forget', ['id' => $uuid]);

        session()->flash('status', __('Failed job removed.'));
    }

    protected function tableExists(): bool
    {
        return Schema::hasTable('failed_jobs');
    }

    public function render()
    {
        $jobs = $this->tableExists()
            ? DB::table('failed_jobs')->orderByDesc('id')->paginate(15)
            : new LengthAwarePaginator([], 0, 15);

        return view('electrik::livewire.ops.failed-jobs', [
            'jobs' => $jobs,
            'tableExists' => $this->tableExists(),
        ]);
    }
}
