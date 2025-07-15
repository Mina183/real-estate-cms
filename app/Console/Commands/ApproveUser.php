<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApproveUser extends Command
{
    protected $signature = 'user:approve {id}';
    protected $description = 'Manually approve a user and set role';

    public function handle()
    {
        $id = $this->argument('id');

        $updated = DB::table('users')->where('id', $id)->update([
            'is_approved' => true,
            'role' => 'channel_partner',
            'requested_role' => 'channel_partner',
        ]);

        if ($updated) {
            $this->info("User ID {$id} approved successfully.");
        } else {
            $this->error("Failed to approve user or user not found.");
        }
    }
}