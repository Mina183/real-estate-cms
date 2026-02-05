<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password for all test users (change in production!)
        $password = Hash::make('password123');

        $users = [
            // INTERNAL STAFF
            [
                'name' => 'Fund Manager',
                'email' => 'admin@triton.test',
                'password' => $password,
                'role' => 'admin',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'operations@triton.test',
                'password' => $password,
                'role' => 'operations',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Compliance Officer',
                'email' => 'compliance@triton.test',
                'password' => $password,
                'role' => 'compliance_officer',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Relationship Manager',
                'email' => 'relationship@triton.test',
                'password' => $password,
                'role' => 'relationship_manager',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Data Room Admin',
                'email' => 'dataroom@triton.test',
                'password' => $password,
                'role' => 'data_room_administrator',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
            
            // EXTERNAL
            [
                'name' => 'External Auditor',
                'email' => 'auditor@external.test',
                'password' => $password,
                'role' => 'auditor',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Legal Counsel',
                'email' => 'counsel@external.test',
                'password' => $password,
                'role' => 'external_counsel',
                'is_approved' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            // Only create if doesn't exist
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Created test users with different roles');
        $this->command->info('📧 All users use password: password123');
        $this->command->info('🔐 Login emails:');
        $this->command->info('   - superadmin@poseidon.test');
        $this->command->info('   - admin@poseidon.test');
        $this->command->info('   - operations@poseidon.test');
        $this->command->info('   - compliance@poseidon.test');
        $this->command->info('   - dataroom@poseidon.test');
    }
}
