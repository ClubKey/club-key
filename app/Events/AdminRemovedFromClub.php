<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Club;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class AdminRemovedFromClub
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Club $club,
        public User $admin
    ) {}
}
