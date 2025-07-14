<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DeleteSuperadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
public function handle()
{
    $email = 'mk@poseidonhumancapital.com'; // <-- replace with actual superadmin email
    $user = \App\Models\User::where('email', $email)->first();

    if ($user) {
        $user->delete();
        $this->info("Superadmin with email {$email} deleted.");
    } else {
        $this->error("User with email {$email} not found.");
    }

    return 0;
}
}
