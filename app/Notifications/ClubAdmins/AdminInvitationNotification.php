<?php

declare(strict_types=1);

namespace App\Notifications\ClubAdmins;

use App\Models\AdminInvitation;
use App\Models\Club;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use function route;

final class AdminInvitationNotification extends Notification
{
    use Queueable;

    private Club $club;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AdminInvitation $adminInvitation
    ) {
        $this->club = $adminInvitation->club;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line("You've been invited to be an admin of a club.")
            ->line("{$this->inviterName()} has invited you to be an admin of {$this->adminInvitation->club->name}")
            ->action('Accept Invitation', url(route('clubs.admin-invitations.accept', [
                'adminInvitation' => $this->adminInvitation->id,
            ], false)))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    private function inviterName(): string
    {
        return $this->adminInvitation->inviter?->name ?? 'Someone';
    }
}
