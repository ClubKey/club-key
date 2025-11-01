<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

final class PromotionPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Promotion $promotion,
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
        return ['mail', 'vonage'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->promotion->club->name} has something for you.")
            ->greeting("Hey {$notifiable->name},")
            ->line("{$this->promotion->club->name} has something for you since you are part of their club.")
            ->line($this->promotion->name)
            ->line($this->promotion->description)
            ->action('View Your Card', url(route('clubs.show', $this->promotion->club->slug)))
            ->line('Hope you love it!');
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

    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->clientReference($this->promotion->club->slug)
            ->content($this->getTextContent());
    }

    private function getTextContent(): string
    {
        $link = $this->promotion->link ?: $this->promotion->club->route;

        return "{$this->promotion->name} {$this->promotion->description} {$link}";
    }
}
