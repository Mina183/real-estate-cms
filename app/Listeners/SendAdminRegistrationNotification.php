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
        // ✅ CORRECTED: whereIn for multiple roles
        Log::info("Listener triggered for new user: {$event->user->email}");
        $admins = User::whereIn('role', ['superadmin', 'admin'])->get();

        foreach ($admins as $admin) {
            \Log::info("Attempting to send to: {$admin->email}");

            try {
                Mail::to($admin->email)->send(
                    new NewUserNeedsApproval($event->user)
                );
                \Log::info("Mail sent to: {$admin->email}");
            } catch (\Exception $e) {
                \Log::error("Failed to send mail to: {$admin->email} — " . $e->getMessage());
            }
        }

            }
}