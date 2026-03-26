<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ContactType;
use App\Enums\Topic;
use App\Models\ClubUser;
use App\Models\RewardLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

use function can_send_notification;
use function is_vonage_available;

final class YourRewardLevelHasChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ClubUser $clubUser,
        public ?RewardLevel $previousRewardLevel
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
        $channels = [];

        // Check email subscription
        if (can_send_notification($notifiable, $this->clubUser->club, ContactType::EMAIL, Topic::NOTIFICATIONS)) {
            $channels[] = 'mail';
        }

        // Check SMS subscription
        if (is_vonage_available() && $notifiable->phone &&
            can_send_notification($notifiable, $this->clubUser->club, ContactType::SMS, Topic::NOTIFICATIONS)) {
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
            ->subject($this->subject())
            ->greeting("Hey {$notifiable->first_name},")
            ->line($this->subject())
            ->line($this->getNewRewardLine())
            ->line("ClubKey tracks visits per month and gives you a discount on your service. When you visit and show us your card, you'll get rewards and discounts. The more you visit, the more you'll get. When you visit not as much as you did previously, you can lose previous discounts.")
            ->line($this->getEndingLine())
            ->action('View Your Card', url($this->clubUser->club->route));
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->clientReference($this->clubUser->club->slug)
            ->content($this->getTextContent());
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

    private function subject(): string
    {
        if ($this->hasIncreasedRewards()) {
            return "Congratulations! You're getting rewarded for being so loyal to {$this->getClubName()}!";
        }

        if ($this->clubUser->rewardLevel === null) {
            return "Bummer, you've lost your rewards for now.";
        }

        return "Bummer, you've been downgraded to a lower reward level.";
    }

    private function hasIncreasedRewards(): bool
    {
        if ($this->previousRewardLevel === null && $this->clubUser->rewardLevel !== null) {
            return true;
        }

        if ($this->clubUser->rewardLevel === null) {
            return false;
        }

        if ($this->previousRewardLevel->min_visits_per_month > $this->clubUser->rewardLevel->min_visits_per_month) {
            return false;
        }

        return true;
    }

    private function getClubName(): string
    {
        return $this->clubUser->club->name;
    }

    private function getNewRewardLine(): string
    {

        if ($this->hasIncreasedRewards()) {
            return "You're now a {$this->clubUser->rewardLevel->name} member at {$this->getClubName()}, enjoy your new perks! When you visit and show us your card, you'll get {$this->getPercentageText()} off your service.";
        }

        return 'Check in on your next visit to continue earning rewards.';
    }

    private function getPercentageText(): string
    {
        if ($this->clubUser->rewardLevel === null) {
            return '0%';
        }

        return $this->clubUser->rewardLevel->percentage_discount.'%';
    }

    private function getEndingLine(): string
    {
        $clubName = $this->getClubName();

        return "Thanks so much for being a loyal member of $clubName. We truly appreciate your continued support and enthusiasm. Hope to see you soon!";
    }

    private function getTextContent(): string
    {
        $text = $this->hasIncreasedRewards() ? "You're getting rewarded for being so loyal to {$this->getClubName()}! You're now a {$this->clubUser->rewardLevel->name} member. Show your card and get {$this->getPercentageText()} off your next purchase!" :
            "We miss you at {$this->getClubName()}. You've been moved to a lower reward tier. Check in on your next visit to keep earning rewards.";

        $clubURL = $this->clubUser->club->route;

        return "$text $clubURL";
    }
}
