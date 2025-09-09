<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PartnerDocument;
use App\Models\User;

class PartnerResponseSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public PartnerDocument $document;
    public User $partner;
    public User $admin;

    /**
     * Create a new message instance.
     */
    public function __construct(PartnerDocument $document, User $partner, User $admin)
    {
        $this->document = $document;
        $this->partner = $partner;
        $this->admin = $admin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Partner Response Received: ' . $this->document->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.documents.partner-response-submitted',
        );
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
}