<?php

namespace App\Notifications;

use App\Models\DocumentAccessRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewAccessRequestNotification extends Notification
{
    public function __construct(public DocumentAccessRequest $accessRequest) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $investor = $this->accessRequest->link?->investor;
        $package  = $this->accessRequest->link?->package;

        return (new MailMessage)
            ->subject('New Document Access Request — ' . ($investor?->organization_name ?? $investor?->legal_entity_name ?? 'Investor'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new document access request has been submitted and requires your review.')
            ->line('**Requester:** ' . $this->accessRequest->requester_name . ' (' . $this->accessRequest->requester_email . ')')
            ->line('**Package:** ' . ($package?->name ?? '—'))
            ->line('**Investor:** ' . ($investor?->organization_name ?? $investor?->legal_entity_name ?? '—'))
            ->line('**Submitted at:** ' . $this->accessRequest->created_at->format('d M Y, H:i'))
            ->action('Review Request', route('document-access-requests.index'))
            ->line('Please approve or reject this request at your earliest convenience.');
    }
}
