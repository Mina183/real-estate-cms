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

class NewDocumentAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public PartnerDocument $document;
    public User $uploader;
    public User $partner;

    /**
     * Create a new message instance.
     */
    public function __construct(PartnerDocument $document, User $uploader, User $partner)
    {
        $this->document = $document;
        $this->uploader = $uploader;
        $this->partner = $partner;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Document Assigned: ' . $this->document->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.documents.new-document-assigned',
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