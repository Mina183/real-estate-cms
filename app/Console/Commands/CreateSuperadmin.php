<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperadmin extends Command
{
    protected $signature = 'make:superadmin';
    protected $description = 'Create the first Superadmin user';

    public function handle()
    {
        $email = $this->ask('Enter Superadmin email');
        $password = $this->secret('Enter password');

        $user = User::create([
            'name' => 'Superadmin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'Superadmin',
            'is_approved' => true,
        ]);

        $this->info("Superadmin created: {$user->email}");
        return 0;
    }
}
