<?php

namespace Electrik\Livewire\Ops\Announcements;

use Electrik\Actions\Announcements\PublishAnnouncement;
use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Announcements')]
class Index extends Component
{
    use AuthorizesOperatorAccess;

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function publish(int $announcementId, PublishAnnouncement $publishAnnouncement): void
    {
        $this->authorizeOperator();

        $announcement = Announcement::query()->findOrFail($announcementId);
        $notified = $publishAnnouncement->execute($announcement);

        session()->flash('status', trans_choice(
            'Announcement sent to :count person.|Announcement sent to :count people.',
            $notified,
            ['count' => $notified]
        ));
    }

    public function delete(int $announcementId): void
    {
        $this->authorizeOperator();

        Announcement::query()->whereKey($announcementId)->delete();

        session()->flash('status', __('Announcement deleted.'));
    }

    public function render()
    {
        return view('electrik::livewire.ops.announcements.index', [
            'announcements' => Announcement::query()->latest()->get(),
        ]);
    }
}
