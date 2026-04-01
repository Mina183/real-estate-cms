<?php

namespace App\Notifications;

use App\Models\DocumentAccessRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentAccessApprovedNotification extends Notification
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

        $investorName = $investor?->organization_name ?? $investor?->legal_entity_name ?? 'Unknown Investor';

        return (new MailMessage)
            ->subject('Document Access Approved — ' . $investorName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A document access request has been approved for your attention.')
            ->line('**Investor:** ' . $investorName)
            ->line('**Package:** ' . ($package?->name ?? '—'))
            ->line('**Requester:** ' . $this->accessRequest->requester_name . ' (' . $this->accessRequest->requester_email . ')')
            ->line('**Access expires:** ' . $this->accessRequest->expires_at?->format('d M Y, H:i'))
            ->action('View Investor', route('investors.show', $investor))
            ->line('This is an automated notification from the document access system.');
    }
}
