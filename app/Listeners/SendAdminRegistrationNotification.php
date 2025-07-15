<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewUserNeedsApproval;
use App\Models\User;

class SendAdminRegistrationNotification
{
    public function handle(Registered $event): void
    {
        Log::info("Listener triggered for new user: {$event->user->email}");

        $admins = User::whereIn('role', ['superadmin', 'admin'])->get();

        foreach ($admins as $admin) {
            Log::info("Attempting to send to: {$admin->email}");
            try {
                Mail::to($admin->email)->send(
                    new NewUserNeedsApproval($event->user)
                );
                Log::info("Mail sent to: {$admin->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send mail to: {$admin->email} — " . $e->getMessage());
            }
        }
    }
}