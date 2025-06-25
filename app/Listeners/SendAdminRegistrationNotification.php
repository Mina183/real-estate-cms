<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserNeedsApproval;

class SendAdminRegistrationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(Registered $event): void
    {
        Mail::to('your-email@example.com')->send(
            new NewUserNeedsApproval($event->user)
        );
    }
}
