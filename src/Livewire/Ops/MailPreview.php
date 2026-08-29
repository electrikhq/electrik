<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Notifications\MagicLinkNotification;
use Electrik\Notifications\NewLoginAlertNotification;
use Electrik\Support\UserModel;
use Illuminate\Notifications\Messages\MailMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Email preview')]
class MailPreview extends Component
{
    use AuthorizesOperatorAccess;

    public string $template = 'magic_link';

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function render()
    {
        return view('electrik::livewire.ops.mail-preview', [
            'templates' => [
                'magic_link' => __('Magic link'),
                'login_alert' => __('New login alert'),
                'team_invite' => __('Team invitation'),
            ],
            'html' => $this->renderTemplateHtml(),
        ]);
    }

    protected function renderTemplateHtml(): string
    {
        $user = UserModel::query()->first() ?? (object) [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ];

        try {
            $mail = match ($this->template) {
                'login_alert' => (new NewLoginAlertNotification(
                    ip: '203.0.113.10',
                    userAgent: 'Mozilla/5.0 (Macintosh)',
                    at: now(),
                ))->toMail($user),
                'team_invite' => (new MailMessage)
                    ->subject(__('Invitation to join :team', ['team' => 'Demo Team']))
                    ->greeting(__('Hello!'))
                    ->line(__(':inviter invited you to join :team as :role.', [
                        'inviter' => 'Alex',
                        'team' => 'Demo Team',
                        'role' => 'Member',
                    ]))
                    ->action(__('Accept invitation'), url('/teams/invitations/preview')),
                default => (new MagicLinkNotification(url('/auth/magic-link/preview-token')))->toMail($user),
            };
        } catch (\Throwable $e) {
            return e($e->getMessage());
        }

        return $mail instanceof MailMessage ? $mail->render() : e(__('Unable to render.'));
    }
}
