<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

use function is_vonage_available;
use function url;

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
        $channels = ['mail'];

        if (is_vonage_available() && $notifiable->phone) {
            $channels[] = 'vonage';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from('contact@promotions.clubkey.site', 'ClubKey Deals')
            ->replyTo($this->promotion->club->email, $this->promotion->club->name)
            ->subject("{$this->promotion->club->name} has an exclusive deal for you!")
            ->greeting("Hey {$notifiable->name},")
            ->line("{$this->promotion->club->name} has something for you since you are part of their club.")
            ->line($this->promotion->name)
            ->line($this->promotion->description)
            ->action($this->actionText(), $this->actionLink())
            ->line('Hope you love it!');
    }

    private function actionText(): string
    {
        if ($this->promotion->link) {
            return 'Check it out';
        }

        return 'View Your Card';
    }

    private function actionLink(): string
    {
        return url($this->promotion->public_link);
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

        return "{$this->promotion->description} {$link}, from {$this->promotion->club->name}";
    }
}
