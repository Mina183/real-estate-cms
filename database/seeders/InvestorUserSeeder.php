<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Investor;
use App\Models\InvestorUser;
use Illuminate\Support\Facades\Hash;

class InvestorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first investor from database (or create one if none exists)
        $investor = Investor::first();

        if (!$investor) {
            // Create a test investor if none exists
            $investor = Investor::create([
                'investor_type' => 'individual',
                'organization_name' => 'Test Investor LLC',
                'jurisdiction' => 'United States',
                'stage' => 'active',
                'status' => 'qualified',
                'lifecycle_status' => 'active',
                'target_commitment_amount' => 1000000,
                'final_commitment_amount' => 1000000,
                'funded_amount' => 500000,
                'currency' => 'USD',
                'data_room_access_level' => 'subscribed',
                'data_room_access_granted' => true,
                'data_room_access_granted_at' => now(),
                'kyc_status' => 'complete',
                'is_professional_client' => true,
                'sanctions_check_passed' => true,
            ]);
        }

        // Check if investor user already exists
        $existingUser = InvestorUser::where('investor_id', $investor->id)->first();

        if ($existingUser) {
            $this->command->info("Investor user already exists for investor ID {$investor->id}");
            $this->command->info("Email: {$existingUser->email}");
            return;
        }

        // Create investor user account
        $investorUser = InvestorUser::create([
            'investor_id' => $investor->id,
            'name' => $investor->organization_name ?? 'Test Investor',
            'email' => 'investor@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->command->info('✅ Test investor user created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Email: investor@test.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('URL: /investor/login');
    }
}