<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\CommunicationSubscriptions\LinkSubscriptionsToUserAction;
use Illuminate\Auth\Events\Registered;

final readonly class LinkSubscriptionsOnRegistered
{
    public function __construct(
        private LinkSubscriptionsToUserAction $linkSubscriptionsToUserAction,
    ) {}

    public function handle(Registered $event): void
    {
        $this->linkSubscriptionsToUserAction->handle($event->user);
    }
}
