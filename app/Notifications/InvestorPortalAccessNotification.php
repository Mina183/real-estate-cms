<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InvestorPortalAccessNotification extends Notification
{
    public function __construct(
        public string $email,
        public string $password,
        public string $loginUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Investor Portal Access – Triton Real Estate Fund')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your investor portal account has been created. You can now access your documents and portfolio information.')
            ->line('**Login Details:**')
            ->line('Email: ' . $this->email)
            ->line('Temporary Password: ' . $this->password)
            ->action('Access Investor Portal', $this->loginUrl)
            ->line('For security, please change your password after your first login.')
            ->line('If you have any questions, please contact your relationship manager.');
    }
}