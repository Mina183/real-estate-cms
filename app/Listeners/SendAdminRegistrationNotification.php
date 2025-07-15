<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewUserNeedsApproval;
use App\Models\User;

class SendAdminRegistrationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(Registered $event): void
    {
        \Log::info('✅ Listener is handling Registered event for: ' . $event->user->email);

        $admins = \App\Models\User::whereIn('role', ['superadmin', 'admin'])->get();

        foreach ($admins as $admin) {
            \Log::info("Attempting to send to: {$admin->email}");

            try {
                \Mail::to($admin->email)->send(new \App\Mail\NewUserNeedsApproval($event->user));
                \Log::info("Mail sent to: {$admin->email}");
            } catch (\Exception $e) {
                \Log::error("❌ Failed to send mail to {$admin->email}: " . $e->getMessage());
            }
        }
    }
}