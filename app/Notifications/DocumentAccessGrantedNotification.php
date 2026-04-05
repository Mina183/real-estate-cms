<?php

namespace App\Notifications;

use App\Models\DocumentAccessRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentAccessGrantedNotification extends Notification
{
    public function __construct(public DocumentAccessRequest $accessRequest) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $package   = $this->accessRequest->link?->package;
        $accessUrl = route('doc-access.show', $this->accessRequest->link->token);

        return (new MailMessage)
            ->subject('Your Document Access Has Been Approved — Triton Real Estate Fund')
            ->greeting('Dear ' . $this->accessRequest->requester_name . ',')
            ->line('Your request to access **' . ($package?->name ?? 'fund materials') . '** has been approved.')
            ->line('You can access the documents using the link below. Please note that access is valid for **48 hours** from the time of approval.')
            ->action('Access Documents', $accessUrl)
            ->line('Access expires: **' . $this->accessRequest->expires_at?->format('d M Y \a\t H:i') . '**')
            ->line('If your access expires, please contact your relationship manager to request a new link.')
            ->line('These materials are confidential and intended solely for your use. Please do not share this link.');
    }
}
