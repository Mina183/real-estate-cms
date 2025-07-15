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
        Log::info("Listener triggered for new user: {$event->user->email}");

        $admins = User::whereIn('role', ['superadmin', 'admin'])->get();

        foreach ($admins as $admin) {
            Log::info("Attempting to send to: {$admin->email}");

            try {
                // 🚨 FORCED FAILURE — send to invalid address
                Mail::to('fail@test')->send(
                    new NewUserNeedsApproval($event->user)
                );

                Log::info("Mail (forced) sent to: fail@test");
            } catch (\Exception $e) {
                Log::error("Mail send failure for {$admin->email}: " . $e->getMessage());
            }
        }
    }
}