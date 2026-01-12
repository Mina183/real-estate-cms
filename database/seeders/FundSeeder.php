<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fund;

class FundSeeder extends Seeder
{
    public function run(): void
    {
        Fund::create([
            'fund_name' => 'Triton Real Estate Fund (CEIC) Limited',
            'fund_number' => 'TREFC-001',
            'vintage_year' => 2023,
            'total_size' => 50000000.00, // $50M
            'currency' => 'USD',
            'status' => 'active',
            'inception_date' => '2023-01-01',
            'description' => 'Real estate investment fund focused on UAE and GCC markets',
        ]);
    }
}
