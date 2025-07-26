<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\AdminInvitation;
use App\Models\Club;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

final class InviteAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public Club $club;

    public ?User $inviter;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public AdminInvitation $adminInvitation,
    ) {
        $this->club = $adminInvitation->club;
        $this->inviter = $adminInvitation->inviter;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->inviterName()} has invited you to be an admin of {$this->club->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.clubs.invite-admin',
            with: [
                'inviterName' => $this->inviterName(),
                'clubName' => $this->club->name,
                'url' => URL::signedRoute('clubs.admin-invitations.accept', [
                    'club' => $this->club,
                    'adminInvitation' => $this->adminInvitation,
                ], false),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    private function inviterName(): string
    {
        return $this->inviter?->name ?? 'Someone';
    }
}
