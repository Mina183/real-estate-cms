<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataRoomStructureSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('data_room_documents')->truncate();
        DB::table('data_room_folders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $folders = [
            [
                'number' => '1',
                'name'   => 'Marketing & Overview',
                'desc'   => 'Teaser documents, executive summaries, presentation decks, fund overview materials — investor-facing',
                'level'  => 'public',
            ],
            [
                'number' => '2',
                'name'   => 'Fund Legal & Constitutional Documents',
                'desc'   => 'Certificate of Incorporation, Certificate of Incumbency, Articles of Association, Offering Letter — investor-facing',
                'level'  => 'public',
            ],
            [
                'number' => '3',
                'name'   => 'Governance & Key Personnel',
                'desc'   => 'Board composition, key personnel profiles, governance framework, management structure — investor-facing',
                'level'  => 'public',
            ],
            [
                'number' => '4',
                'name'   => 'Compliance, Risk & Policies',
                'desc'   => 'Compliance framework, risk policies, regulatory filings, AML/KYC policies — restricted selective',
                'level'  => 'restricted',
            ],
            [
                'number' => '5',
                'name'   => 'Market Research & Investment Thesis',
                'desc'   => 'Market analysis, investment thesis documentation, sector research, asset pipeline rationale — investor-facing',
                'level'  => 'public',
            ],
            [
                'number' => '6',
                'name'   => 'Neptune Associates',
                'desc'   => 'Neptune Associates materials, co-investment documentation, partnership information — investor-facing',
                'level'  => 'public',
            ],
            [
                'number' => '7',
                'name'   => 'Fund Waterfall',
                'desc'   => 'Waterfall mechanics, distribution models, carried interest calculations — restricted selective',
                'level'  => 'restricted',
            ],
            [
                'number' => '8',
                'name'   => 'Subscription & Onboarding Pack',
                'desc'   => 'PPM, Subscription Agreement, Application Form, Self Certification, KYC/AML Questionnaire — investor-facing',
                'level'  => 'public',
            ],
            [
                'number' => '9',
                'name'   => 'Reporting, DDQ & Investor Communications',
                'desc'   => 'Quarterly NAV reports, due diligence questionnaires, investor letters and communications — restricted selective',
                'level'  => 'restricted',
            ],
            [
                'number' => '10',
                'name'   => 'Operations & Service Providers',
                'desc'   => 'Fund administrator, custodian, auditor, legal counsel details and service agreements — restricted selective',
                'level'  => 'restricted',
            ],
            [
                'number' => '11',
                'name'   => 'Investor Targeting & Pipeline',
                'desc'   => 'Internal investor pipeline, targeting strategy, CRM data, prospect lists — restricted internal',
                'level'  => 'confidential',
            ],
            [
                'number' => '12',
                'name'   => 'Investor Personal Documents',
                'desc'   => 'Documents specific to individual investors — on request only',
                'level'  => 'highly_confidential',
            ],
        ];

        foreach ($folders as $index => $folder) {
            DB::table('data_room_folders')->insert([
                'folder_number'    => $folder['number'],
                'folder_name'      => $folder['name'],
                'parent_folder_id' => null,
                'order'            => $index + 1,
                'description'      => $folder['desc'],
                'access_level'     => $folder['level'],
                'is_active'        => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}
