<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\CommunicationSubscription;
use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

use function can_send_notification;
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
        public ?CommunicationSubscription $subscription = null,
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
        if ($this->subscription) {
            if ($this->subscription->status !== SubscriptionStatus::SUBSCRIBED) {
                return [];
            }

            return match ($this->subscription->contact_type) {
                ContactType::EMAIL => ['mail'],
                ContactType::SMS => is_vonage_available() ? ['vonage'] : [],
                default => [],
            };
        }

        $channels = [];

        if ($notifiable->email ?? null) {
            $emailSubscription = CommunicationSubscription::query()
                ->where('club_id', $this->promotion->club_id)
                ->forContact(ContactType::EMAIL, $notifiable->email)
                ->forTopic(Topic::PROMOTIONS)
                ->first();

            if (can_send_notification($emailSubscription)) {
                $channels[] = 'mail';
            }
        }

        if (is_vonage_available() && ($notifiable->phone ?? null)) {
            $smsSubscription = CommunicationSubscription::query()
                ->where('club_id', $this->promotion->club_id)
                ->forContact(ContactType::SMS, $notifiable->phone)
                ->forTopic(Topic::PROMOTIONS)
                ->first();

            if (can_send_notification($smsSubscription)) {
                $channels[] = 'vonage';
            }
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->name ?? $notifiable->userName ?? null;

        $message = (new MailMessage)
            ->from('contact@promotions.clubkey.site', 'ClubKey Deals')
            ->replyTo($this->promotion->club->email, $this->promotion->club->name)
            ->subject("{$this->promotion->club->name} has an exclusive deal for you!")
            ->greeting($name ? "Hey {$name}," : 'Hey there,')
            ->line("{$this->promotion->club->name} has something for you since you are part of their club.")
            ->line($this->promotion->name)
            ->line($this->promotion->description)
            ->action($this->actionText(), $this->actionLink())
            ->line('Hope you love it!');

        // Add unsubscribe link if subscription exists
        $subscription = $this->getSubscription($notifiable);
        if ($subscription) {
            $unsubscribeUrl = URL::signedRoute('unsubscribe.email', ['subscription' => $subscription->id]);
            $message->line("If you no longer wish to receive these emails, you can [unsubscribe]({$unsubscribeUrl}).");
        }

        return $message;
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

    private function getSubscription(object $notifiable): ?CommunicationSubscription
    {
        if ($this->subscription) {
            return $this->subscription;
        }

        $email = $notifiable->email ?? null;

        if (! $email) {
            return null;
        }

        return CommunicationSubscription::where('club_id', $this->promotion->club->id)
            ->where('contact_type', ContactType::EMAIL)
            ->where('contact_value', mb_strtolower($email))
            ->where('topic', Topic::PROMOTIONS)
            ->first();
    }

    private function getTextContent(): string
    {
        $link = $this->promotion->link ?: $this->promotion->club->route;

        return "{$this->promotion->description} {$link}, from {$this->promotion->club->name}";
    }
}
