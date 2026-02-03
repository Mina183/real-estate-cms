<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CapitalCall;
use App\Models\Distribution;
use App\Models\PaymentTransaction;
use App\Models\Investor;

class Phase4Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active investors (assume you have some investors in the database)
        $investors = Investor::where('stage', 'active')
            ->where('final_commitment_amount', '>', 0)
            ->get();

        if ($investors->count() === 0) {
            $this->command->warn('No active investors found. Creating sample investors first...');
            
            // Create 3 sample investors if none exist
            $investors = collect([
                Investor::create([
                    'investor_type' => 'institutional',
                    'organization_name' => 'Alpha Capital Partners',
                    'legal_entity_name' => 'Alpha Capital Partners LLC',
                    'jurisdiction' => 'Delaware, USA',
                    'stage' => 'active',
                    'status' => 'active',
                    'lifecycle_status' => 'active',
                    'final_commitment_amount' => 5000000,
                    'funded_amount' => 0,
                    'currency' => 'USD',
                    'is_professional_client' => true,
                    'sanctions_check_passed' => true,
                    'kyc_status' => 'completed',
                    'bank_account_verified' => true,
                    'activated_date' => now()->subMonths(6),
                ]),
                Investor::create([
                    'investor_type' => 'institutional',
                    'organization_name' => 'Beta Investment Fund',
                    'legal_entity_name' => 'Beta Investment Fund Ltd',
                    'jurisdiction' => 'Cayman Islands',
                    'stage' => 'active',
                    'status' => 'active',
                    'lifecycle_status' => 'active',
                    'final_commitment_amount' => 3000000,
                    'funded_amount' => 0,
                    'currency' => 'USD',
                    'is_professional_client' => true,
                    'sanctions_check_passed' => true,
                    'kyc_status' => 'completed',
                    'bank_account_verified' => true,
                    'activated_date' => now()->subMonths(4),
                ]),
                Investor::create([
                    'investor_type' => 'individual',
                    'organization_name' => null,
                    'legal_entity_name' => 'Charles Henderson',
                    'jurisdiction' => 'UAE',
                    'stage' => 'active',
                    'status' => 'active',
                    'lifecycle_status' => 'active',
                    'final_commitment_amount' => 2000000,
                    'funded_amount' => 0,
                    'currency' => 'USD',
                    'is_professional_client' => true,
                    'sanctions_check_passed' => true,
                    'kyc_status' => 'completed',
                    'bank_account_verified' => true,
                    'activated_date' => now()->subMonths(3),
                ]),
            ]);
        }

        $this->command->info('Creating Capital Calls...');

        // Capital Call 1 - Issued and Partially Paid
        $cc1 = CapitalCall::create([
            'call_number' => 'CC-2026-001',
            'title' => 'Q1 2026 Capital Call - Property Acquisition',
            'description' => 'Capital call for acquiring commercial property portfolio in Dubai Marina',
            'total_amount' => 4000000,
            'call_date' => now()->subDays(15),
            'due_date' => now()->addDays(15),
            'status' => 'partially_paid',
            'total_received' => 2000000,
        ]);

        // Create payment transactions for CC1
        foreach ($investors as $index => $investor) {
            $percentage = $investor->final_commitment_amount / $investors->sum('final_commitment_amount');
            $amount = $cc1->total_amount * $percentage;
            
            PaymentTransaction::create([
                'transactionable_id' => $cc1->id,
                'transactionable_type' => CapitalCall::class,
                'investor_id' => $investor->id,
                'transaction_type' => 'capital_call',
                'amount' => $amount,
                'commitment_percentage' => ($amount / $investor->final_commitment_amount) * 100,
                'status' => $index === 0 ? 'paid' : 'pending', // First investor paid
                'due_date' => $cc1->due_date,
                'paid_date' => $index === 0 ? now()->subDays(5) : null,
                'payment_method' => $index === 0 ? 'wire_transfer' : null,
                'reference_number' => $index === 0 ? 'WIRE-' . rand(100000, 999999) : null,
            ]);

            // Update investor funded amount for paid transactions
            if ($index === 0) {
                $investor->increment('funded_amount', $amount);
            }
        }

        $this->command->info('Created CC-2026-001 (Partially Paid)');

        // Capital Call 2 - Issued and Pending
        $cc2 = CapitalCall::create([
            'call_number' => 'CC-2026-002',
            'title' => 'Q1 2026 Capital Call - Working Capital',
            'description' => 'Capital call for operational expenses and reserve fund',
            'total_amount' => 1500000,
            'call_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'status' => 'issued',
            'total_received' => 0,
        ]);

        foreach ($investors as $investor) {
            $percentage = $investor->final_commitment_amount / $investors->sum('final_commitment_amount');
            $amount = $cc2->total_amount * $percentage;
            
            PaymentTransaction::create([
                'transactionable_id' => $cc2->id,
                'transactionable_type' => CapitalCall::class,
                'investor_id' => $investor->id,
                'transaction_type' => 'capital_call',
                'amount' => $amount,
                'commitment_percentage' => ($amount / $investor->final_commitment_amount) * 100,
                'status' => 'pending',
                'due_date' => $cc2->due_date,
            ]);
        }

        $this->command->info('Created CC-2026-002 (Issued, All Pending)');

        // Capital Call 3 - Draft
        $cc3 = CapitalCall::create([
            'call_number' => 'CC-2026-003',
            'title' => 'Q2 2026 Capital Call - Development Project',
            'description' => 'Capital call for Phase 2 development project',
            'total_amount' => 6000000,
            'call_date' => now()->addDays(30),
            'due_date' => now()->addDays(60),
            'status' => 'draft',
            'total_received' => 0,
            'notes' => 'Draft - to be reviewed before issuance',
        ]);

        $this->command->info('Created CC-2026-003 (Draft)');

        // ============================================
        // DISTRIBUTIONS
        // ============================================

        $this->command->info('Creating Distributions...');

        // Distribution 1 - Completed
        $dist1 = Distribution::create([
            'distribution_number' => 'DIST-2025-004',
            'title' => 'Q4 2025 Profit Distribution',
            'description' => 'Quarterly profit distribution from rental income',
            'type' => 'dividend',
            'total_amount' => 800000,
            'distribution_date' => now()->subDays(20),
            'record_date' => now()->subDays(27),
            'status' => 'completed',
            'total_distributed' => 800000,
        ]);

        foreach ($investors as $investor) {
            if ($investor->funded_amount > 0) {
                $percentage = $investor->funded_amount / $investors->where('funded_amount', '>', 0)->sum('funded_amount');
                $amount = $dist1->total_amount * $percentage;
                
                PaymentTransaction::create([
                    'transactionable_id' => $dist1->id,
                    'transactionable_type' => Distribution::class,
                    'investor_id' => $investor->id,
                    'transaction_type' => 'distribution',
                    'amount' => $amount,
                    'status' => 'paid',
                    'due_date' => $dist1->distribution_date,
                    'paid_date' => $dist1->distribution_date,
                    'payment_method' => 'wire_transfer',
                    'reference_number' => 'DIST-WIRE-' . rand(100000, 999999),
                ]);
            }
        }

        $this->command->info('Created DIST-2025-004 (Completed)');

        // Distribution 2 - Processing
        $dist2 = Distribution::create([
            'distribution_number' => 'DIST-2026-001',
            'title' => 'Q1 2026 Profit Distribution',
            'description' => 'Quarterly profit distribution',
            'type' => 'profit_share',
            'total_amount' => 500000,
            'distribution_date' => now()->addDays(5),
            'record_date' => now()->subDays(2),
            'status' => 'processing',
            'total_distributed' => 250000,
        ]);

        foreach ($investors as $index => $investor) {
            if ($investor->funded_amount > 0) {
                $percentage = $investor->funded_amount / $investors->where('funded_amount', '>', 0)->sum('funded_amount');
                $amount = $dist2->total_amount * $percentage;
                
                PaymentTransaction::create([
                    'transactionable_id' => $dist2->id,
                    'transactionable_type' => Distribution::class,
                    'investor_id' => $investor->id,
                    'transaction_type' => 'distribution',
                    'amount' => $amount,
                    'status' => $index === 0 ? 'paid' : 'pending', // First one paid
                    'due_date' => $dist2->distribution_date,
                    'paid_date' => $index === 0 ? now() : null,
                    'payment_method' => $index === 0 ? 'wire_transfer' : null,
                ]);
            }
        }

        $this->command->info('Created DIST-2026-001 (Processing)');

        // Distribution 3 - Draft
        $dist3 = Distribution::create([
            'distribution_number' => 'DIST-2026-002',
            'title' => 'Q2 2026 Return of Capital',
            'description' => 'Partial return of capital from property sale',
            'type' => 'return_of_capital',
            'total_amount' => 1200000,
            'distribution_date' => now()->addDays(45),
            'record_date' => now()->addDays(38),
            'status' => 'draft',
            'total_distributed' => 0,
            'notes' => 'Awaiting final approval from investment committee',
        ]);

        $this->command->info('Created DIST-2026-002 (Draft)');

        $this->command->info('✅ Phase 4 seeding completed!');
        $this->command->info('Created:');
        $this->command->info('  - 3 Capital Calls (1 partially paid, 1 issued, 1 draft)');
        $this->command->info('  - 3 Distributions (1 completed, 1 processing, 1 draft)');
        $this->command->info('  - ' . PaymentTransaction::count() . ' Payment Transactions');
    }
}
