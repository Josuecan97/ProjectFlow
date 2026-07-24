<?php

namespace App\Domain\Organizations\Notifications;

use App\Domain\Organizations\Models\OrganizationInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OrganizationInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly OrganizationInvitation $invitation,
        private readonly string $token,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Te invitaron a ProjectFlow')
            ->greeting('Hola')
            ->line("Te invitaron a colaborar en {$this->invitation->organization->name}.")
            ->action('Revisar invitación', route('invitations.show', $this->token))
            ->line('La invitación expira en 7 días.');
    }
}
