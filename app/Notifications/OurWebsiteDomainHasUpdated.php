<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class OurWebsiteDomainHasUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly User $user,
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
            ->subject('ClubKey Website Domain Update')
            ->line('Good news! Our website domain has been updated.')
            ->line($this->urlLine())
            ->line("All your clubs including ({$this->getClubNames()}) and rewards are still here, just at a new location.")
            ->line('You can use the same password and login credentials as before.')
            ->action('Login', url('/login'))
            ->line('Thank you for using ClubKey!');
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

    private function urlLine(string $path = ''): string
    {
        $url = url($path);

        return "We've set up a new website domain for your club rewards. Please visit the new domain $url to access your rewards and manage your account.";
    }

    private function getClubNames(): string
    {
        $clubs = $this->user->clubs->select('name');

        return Str::of($clubs->pluck('name')->join(', '))->replaceLast(',', ' and')->__toString();
    }
}
