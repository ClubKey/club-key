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
            ->greeting("Hey {$this->clubUser->user->first_name},")
            ->line($this->subject())
            ->line($this->newRewardLine())
            ->line('Thanks so much for being a loyal member of our club. We truly appreciate your continued support and enthusiasm. Hope to see you soon!');
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
        if ($this->previousRewardLevel === null && $this->clubUser->rewardLevel !== null) {
            return "Congratulations! You've reached a new reward level!";
        }

        if ($this->clubUser->rewardLevel === null) {
            return "Bummer, you've lost your rewards for now. Visit us more often to regain them!";
        }

        if ($this->previousRewardLevel->min_visits_per_month > $this->clubUser->rewardLevel->min_visits_per_month) {
            return "Bummer, you've been downgraded to a lower reward level. Visit us more often to regain your previous status!";
        }

        return "Great news! You've been upgraded to a higher reward level!";
    }

    private function newRewardLine(): string
    {
        if ($this->previousRewardLevel !== null && $this->clubUser->rewardLevel !== null) {
            return "You're now a {$this->clubUser->rewardLevel->name} member, enjoy your new perks! When you visit and show us your card, you'll get {$this->clubUser->rewardLevel->percentage_discount}% off your service.";
        }

        if ($this->clubUser->rewardLevel !== null) {
            return "You're now a {$this->clubUser->rewardLevel->name} member, enjoy your perks! When you visit and show us your card, you'll get {$this->clubUser->rewardLevel->percentage_discount}% off your service.";
        }

        return '';
    }
}
