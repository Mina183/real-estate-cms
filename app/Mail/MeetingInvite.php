<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Meeting;
use App\Models\User;

class MeetingInvite extends Mailable
{
    use Queueable, SerializesModels;

    public Meeting $meeting;
    public User $partner;

    /**
     * Create a new message instance.
     */
    public function __construct(Meeting $meeting, User $partner)
    {
        $this->meeting = $meeting;
        $this->partner = $partner;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('You have been invited to a team meeting')
                    ->markdown('emails.meeting.invite')
                    ->with([
                    'meeting' => $this->meeting,
                    'partner' => $this->partner,
                ]);
    }

}
