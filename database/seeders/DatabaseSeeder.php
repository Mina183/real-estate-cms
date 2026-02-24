<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin user - bez Faker-a
        User::create([
            'name' => 'M183',
            'email' => 'mk@poseidonhumancapital.com',
            'password' => Hash::make('Mina55810'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->call([
            DataRoomStructureSeeder::class,
            UserRolesSeeder::class,
            FundSeeder::class,
            SampleDocumentsSeeder::class,
            Phase4Seeder::class,
            InvestorUserSeeder::class,
        ]);
    }
}