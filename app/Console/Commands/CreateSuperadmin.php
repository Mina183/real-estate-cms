<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperadmin extends Command
{
    protected $signature = 'make:superadmin {email} {password}';
    protected $description = 'Create a new Superadmin user';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (User::where('email', $email)->exists()) {
            $this->error("User with email $email already exists.");
            return 1;
        }

        $user = User::create([
            'name' => 'superadmin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'superadmin',
            'is_approved' => true,
        ]);

        $this->info("Superadmin created: {$user->email}");
        return 0;
    }
}