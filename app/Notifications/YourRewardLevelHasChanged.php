<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\ClubUser;
use App\Models\RewardLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject())
            ->line($this->subject())
            ->line($this->newRewardLine())
            ->line("ClubKey tracks visits per month and gives you a discount on your service. When you visit and show us your card, you'll get rewards and discounts. The more you visit, the more you'll get. When you visit not as much as you did previously, you can lose previous discounts.")
            ->line($this->endingLine());
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
            return "Congratulations! You're getting rewarded for being so loyal to {$this->clubName()}!";
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

    private function clubName(): string
    {
        return $this->clubUser->club->name;
    }

    private function newRewardLine(): string
    {
        if ($this->hasIncreasedRewards() || $this->clubUser->rewardLevel === null) {
            return "You're now a {$this->clubUser->rewardLevel->name} member at {$this->clubName()}, enjoy your new perks! When you visit and show us your card, you'll get {$this->clubUser->rewardLevel->percentage_discount}% off your service.";
        }

        return 'Check in on your next visit to continue earning rewards.';
    }

    private function endingLine(): string
    {
        $clubName = $this->clubName();

        return "Thanks so much for being a loyal member of $clubName. We truly appreciate your continued support and enthusiasm. Hope to see you soon!";
    }
}
