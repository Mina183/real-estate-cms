<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing folders 1–4 with new names and access levels
        DB::table('data_room_folders')->where('folder_number', '1')->update([
            'folder_name'  => 'Marketing & Overview',
            'description'  => 'Teaser documents, executive summaries, presentation decks, fund overview materials — investor-facing',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);

        DB::table('data_room_folders')->where('folder_number', '2')->update([
            'folder_name'  => 'Fund Legal & Constitutional Documents',
            'description'  => 'Certificate of Incorporation, Certificate of Incumbency, Articles of Association, Offering Letter — investor-facing',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);

        DB::table('data_room_folders')->where('folder_number', '3')->update([
            'folder_name'  => 'Governance & Key Personnel',
            'description'  => 'Board composition, key personnel profiles, governance framework, management structure — investor-facing',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);

        DB::table('data_room_folders')->where('folder_number', '4')->update([
            'folder_name'  => 'Compliance, Risk & Policies',
            'description'  => 'Compliance framework, risk policies, regulatory filings, AML/KYC policies — restricted selective',
            'access_level' => 'restricted',
            'updated_at'   => now(),
        ]);

        // Rename Investor Personal Documents folder_number from 5 → 12 (keeps its DB ID and exception config)
        DB::table('data_room_folders')->where('folder_number', '5')->update([
            'folder_number' => '12',
            'order'         => 12,
            'updated_at'    => now(),
        ]);

        // Insert new folders 5–11
        $newFolders = [
            [
                'folder_number' => '5',
                'folder_name'   => 'Market Research & Investment Thesis',
                'description'   => 'Market analysis, investment thesis documentation, sector research, asset pipeline rationale — investor-facing',
                'access_level'  => 'public',
                'order'         => 5,
            ],
            [
                'folder_number' => '6',
                'folder_name'   => 'Neptune Associates',
                'description'   => 'Neptune Associates materials, co-investment documentation, partnership information — investor-facing',
                'access_level'  => 'public',
                'order'         => 6,
            ],
            [
                'folder_number' => '7',
                'folder_name'   => 'Fund Waterfall',
                'description'   => 'Waterfall mechanics, distribution models, carried interest calculations — restricted selective',
                'access_level'  => 'restricted',
                'order'         => 7,
            ],
            [
                'folder_number' => '8',
                'folder_name'   => 'Subscription & Onboarding Pack',
                'description'   => 'PPM, Subscription Agreement, Application Form, Self Certification, KYC/AML Questionnaire — investor-facing',
                'access_level'  => 'public',
                'order'         => 8,
            ],
            [
                'folder_number' => '9',
                'folder_name'   => 'Reporting, DDQ & Investor Communications',
                'description'   => 'Quarterly NAV reports, due diligence questionnaires, investor letters and communications — restricted selective',
                'access_level'  => 'restricted',
                'order'         => 9,
            ],
            [
                'folder_number' => '10',
                'folder_name'   => 'Operations & Service Providers',
                'description'   => 'Fund administrator, custodian, auditor, legal counsel details and service agreements — restricted selective',
                'access_level'  => 'restricted',
                'order'         => 10,
            ],
            [
                'folder_number' => '11',
                'folder_name'   => 'Investor Targeting & Pipeline',
                'description'   => 'Internal investor pipeline, targeting strategy, CRM data, prospect lists — restricted internal',
                'access_level'  => 'confidential',
                'order'         => 11,
            ],
        ];

        foreach ($newFolders as $folder) {
            DB::table('data_room_folders')->insert(array_merge($folder, [
                'parent_folder_id' => null,
                'is_active'        => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]));
        }
    }

    public function down(): void
    {
        // Restore original 4 folders
        DB::table('data_room_folders')->where('folder_number', '1')->update([
            'folder_name'  => 'Marketing Materials',
            'description'  => 'Teaser, Executive Summary, Presentation Deck, Term Sheet, Investment Thesis, Market Views, Newsletter',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);
        DB::table('data_room_folders')->where('folder_number', '2')->update([
            'folder_name'  => 'Fund Constitutional Documents',
            'description'  => 'Certificate of Incorporation, Certificate of Incumbency, Articles of Association, Offering Letter',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);
        DB::table('data_room_folders')->where('folder_number', '3')->update([
            'folder_name'  => 'Offering & Subscription Documents',
            'description'  => 'PPM, Subscription Agreement, Application Form, Self Certification Form, KYC/AML Questionnaire',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);
        DB::table('data_room_folders')->where('folder_number', '4')->update([
            'folder_name'  => 'Reporting',
            'description'  => 'Quarterly NAV Reports and investor communications',
            'access_level' => 'public',
            'updated_at'   => now(),
        ]);

        // Restore Investor Personal folder_number
        DB::table('data_room_folders')->where('folder_number', '12')->update([
            'folder_number' => '5',
            'order'         => 5,
            'updated_at'    => now(),
        ]);

        // Remove the 7 new folders
        DB::table('data_room_folders')
            ->whereIn('folder_number', ['5','6','7','8','9','10','11'])
            ->delete();
    }
};
