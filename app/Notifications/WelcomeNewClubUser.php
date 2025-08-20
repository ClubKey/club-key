<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\ClubUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WelcomeNewClubUser extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ClubUser $clubUser
    ) {
        //
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
            ->subject('Super exciting!')
            ->line("Wow, sweet! You've just joined {$this->clubUser->club->name}!")
            ->line("That is super cool. Did you know that now you'll get rewarded for being so loyal?")
            ->action('View your reward card', url(route('clubs.show', $this->clubUser->club->slug)))
            ->line('Have a great day!');
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
}
